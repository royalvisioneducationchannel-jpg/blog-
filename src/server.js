import path from "node:path";
import fs from "node:fs/promises";
import crypto from "node:crypto";
import express from "express";
import session from "express-session";
import multer from "multer";
import { config, missingConfig } from "./config.js";
import { initStore, getUser, upsertUser, saveUser, encrypt, userToken, UPLOAD_DIR, DEFAULT_SETTINGS } from "./store.js";
import { loginUrl, exchangeCode } from "./meta/oauth.js";
import { GraphError } from "./meta/graph.js";
import * as ads from "./meta/ads.js";
import { runAgent } from "./agent/agent.js";
import { executeTool } from "./agent/tools.js";
import { runDailyReport, startScheduler } from "./automation/scheduler.js";

const missing = missingConfig();
if (missing.length) console.warn(`Missing configuration: ${missing.join(", ")} (see .env.example)`);

await initStore();
const app = express();
if (config.isProduction) app.set("trust proxy", 1);

app.use(express.json({ limit: "5mb" }));
app.use(
  session({
    secret: config.sessionSecret || crypto.randomBytes(32).toString("hex"),
    resave: false,
    saveUninitialized: false,
    cookie: { httpOnly: true, sameSite: "lax", secure: config.isProduction, maxAge: 30 * 86400000 },
  }),
);
app.use(express.static(path.resolve("public")));

const upload = multer({
  storage: multer.memoryStorage(),
  limits: { fileSize: 8 * 1024 * 1024 },
  fileFilter: (_req, file, cb) => cb(null, ["image/jpeg", "image/png"].includes(file.mimetype)),
});

// One agent turn at a time per user, so the conversation history is never interleaved.
const locks = new Map();
function withLock(id, fn) {
  const prev = locks.get(id) || Promise.resolve();
  const next = prev.catch(() => {}).then(fn);
  locks.set(id, next.finally(() => locks.get(id) === next && locks.delete(id)));
  return next;
}

function requireUser(req, res, next) {
  const user = req.session.userId && getUser(req.session.userId);
  const token = user && userToken(user);
  if (!user || !token) return res.status(401).json({ error: "Please connect your Facebook account." });
  req.user = user;
  req.token = token;
  next();
}

// Chat, approvals and reports need Claude; say so clearly instead of failing with a generic error.
function requireAi(_req, res, next) {
  if (missing.includes("ANTHROPIC_API_KEY"))
    return res.status(503).json({ error: "AI is not set up yet: add ANTHROPIC_API_KEY to the .env file and restart the server." });
  next();
}

const publicUser = (u) => ({
  name: u.name,
  adAccountId: u.adAccountId || null,
  adAccountName: u.adAccountName || null,
  pageId: u.pageId || null,
  pageName: u.pageName || null,
  currency: u.currency || null,
  tokenExpiresAt: u.tokenExpiresAt,
  settings: u.settings,
});

const pendingList = (u) => Object.values(u.pendingActions || {}).filter((a) => a.status === "pending").map(({ id, summary }) => ({ id, summary }));

function logChat(user, role, text) {
  user.chatLog ||= [];
  user.chatLog.push({ role, text, at: new Date().toISOString() });
  if (user.chatLog.length > 300) user.chatLog = user.chatLog.slice(-300);
}

// ---------- Auth ----------
app.get("/auth/facebook", (req, res) => {
  if (!config.meta.appId) return res.status(500).send("META_APP_ID is not configured.");
  req.session.oauthState = crypto.randomBytes(16).toString("hex");
  res.redirect(loginUrl(req.session.oauthState));
});

app.get("/auth/facebook/callback", async (req, res) => {
  const { code, state, error_description } = req.query;
  if (error_description) return res.redirect(`/?error=${encodeURIComponent(error_description)}`);
  if (!code || !state || state !== req.session.oauthState) return res.redirect("/?error=Login%20could%20not%20be%20verified.%20Please%20try%20again.");
  delete req.session.oauthState;

  const { accessToken, expiresAt } = await exchangeCode(code);
  const me = await ads.getMe(accessToken);
  await upsertUser(me.id, { name: me.name, tokenEnc: encrypt(accessToken), tokenExpiresAt: expiresAt });
  req.session.regenerate((err) => {
    if (err) return res.redirect("/?error=Session%20error");
    req.session.userId = me.id;
    res.redirect("/");
  });
});

app.post("/auth/logout", (req, res) => req.session.destroy(() => res.json({ ok: true })));

// ---------- Status & setup ----------
app.get("/api/status", (req, res) => {
  const user = req.session.userId && getUser(req.session.userId);
  const connected = Boolean(user && userToken(user));
  res.json({ missingConfig: missing, connected, user: connected ? publicUser(user) : null, defaults: DEFAULT_SETTINGS });
});

app.get("/api/accounts", requireUser, async (req, res) => {
  const [adAccounts, pages] = await Promise.all([ads.listAdAccounts(req.token), ads.listPages(req.token)]);
  res.json({ adAccounts, pages });
});

app.post("/api/select", requireUser, async (req, res) => {
  const { adAccountId, pageId } = req.body || {};
  const [adAccounts, pages] = await Promise.all([ads.listAdAccounts(req.token), ads.listPages(req.token)]);
  const account = adAccounts.find((a) => a.id === adAccountId);
  const page = pages.find((p) => p.id === pageId);
  if (adAccountId && !account) return res.status(400).json({ error: "That ad account is not available for this login." });
  if (pageId && !page) return res.status(400).json({ error: "That Page is not available for this login." });

  const u = req.user;
  if (account) Object.assign(u, { adAccountId: account.id, adAccountName: account.name, currency: account.currency, timezone: account.timezone_name });
  if (page) Object.assign(u, { pageId: page.id, pageName: page.name });
  await saveUser(u);
  res.json({ user: publicUser(u) });
});

app.post("/api/settings", requireUser, async (req, res) => {
  const b = req.body || {};
  const n = (v, d) => (v === undefined || v === "" || Number.isNaN(Number(v)) ? d : Math.max(0, Number(v)));
  const s = req.user.settings;
  req.user.settings = {
    ...s,
    autopilot: b.autopilot === undefined ? s.autopilot : Boolean(b.autopilot),
    maxCpm: n(b.maxCpm, s.maxCpm),
    maxCpc: n(b.maxCpc, s.maxCpc),
    minCtr: n(b.minCtr, s.minCtr),
    maxFrequency: n(b.maxFrequency, s.maxFrequency),
    minSpendForPause: n(b.minSpendForPause, s.minSpendForPause),
    reportLanguage: ["Roman Urdu", "Urdu", "English"].includes(b.reportLanguage) ? b.reportLanguage : s.reportLanguage,
  };
  await saveUser(req.user);
  res.json({ settings: req.user.settings });
});

// ---------- Creative upload ----------
app.post("/api/upload", requireUser, upload.single("image"), async (req, res) => {
  if (!req.file) return res.status(400).json({ error: "Please upload a JPG or PNG image (max 8 MB)." });
  if (!req.user.adAccountId) return res.status(400).json({ error: "Select an ad account first." });

  const base64 = req.file.buffer.toString("base64");
  const { hash, url } = await ads.uploadImage(req.token, req.user.adAccountId, base64);
  const ext = req.file.mimetype === "image/png" ? "png" : "jpg";
  await fs.writeFile(path.join(UPLOAD_DIR, `${hash}.${ext}`), req.file.buffer);

  const asset = {
    hash,
    url,
    name: path.basename(req.file.originalname).slice(0, 120),
    mediaType: req.file.mimetype,
    file: `${hash}.${ext}`,
    accountId: req.user.adAccountId,
    uploadedAt: new Date().toISOString(),
  };
  req.user.assets = [asset, ...(req.user.assets || []).filter((a) => !(a.hash === hash && a.accountId === asset.accountId))];
  await saveUser(req.user);
  res.json({ asset: { hash, url, name: asset.name } });
});

// ---------- Chat ----------
app.get("/api/chat", requireUser, (req, res) => {
  res.json({ messages: req.user.chatLog || [], pending: pendingList(req.user) });
});

app.post("/api/chat", requireUser, requireAi, async (req, res) => {
  const message = String(req.body?.message || "").trim().slice(0, 8000);
  const attachments = Array.isArray(req.body?.attachments) ? req.body.attachments.slice(0, 4) : [];
  if (!message && !attachments.length) return res.status(400).json({ error: "Message is empty." });

  const result = await withLock(req.user.id, async () => {
    const user = getUser(req.user.id);
    const content = [];
    for (const hash of attachments) {
      const asset = (user.assets || []).find((a) => a.hash === hash && a.accountId === user.adAccountId);
      if (!asset) continue;
      const data = await fs.readFile(path.join(UPLOAD_DIR, asset.file), "base64");
      content.push({ type: "image", source: { type: "base64", media_type: asset.mediaType, data } });
      content.push({ type: "text", text: `[App] The user attached the image above: "${asset.name}", image_hash ${asset.hash}.` });
    }
    content.push({ type: "text", text: message || "(see attached image)" });

    logChat(user, "user", message || `📎 ${attachments.length} image(s)`);
    const out = await runAgent(user, req.token, content);
    logChat(user, "assistant", out.reply);
    await saveUser(user);
    return { reply: out.reply, pending: pendingList(user) };
  });
  res.json(result);
});

app.post("/api/chat/reset", requireUser, async (req, res) => {
  await withLock(req.user.id, async () => {
    const user = getUser(req.user.id);
    user.conversation = [];
    user.chatLog = [];
    user.pendingActions = {};
    await saveUser(user);
  });
  res.json({ ok: true });
});

// ---------- Approvals ----------
async function resolveAction(req, res, approved) {
  const result = await withLock(req.user.id, async () => {
    const user = getUser(req.user.id);
    const action = user.pendingActions?.[req.params.id];
    if (!action || action.status !== "pending") return { status: 404, body: { error: "This action is no longer pending." } };

    let note;
    if (approved) {
      try {
        const out = await executeTool(action.tool, action.input, { user, token: req.token });
        action.status = "done";
        note = `[App] The user APPROVED action ${action.id} (${action.summary}). It was executed. Result: ${JSON.stringify(out).slice(0, 4000)}`;
      } catch (err) {
        action.status = "failed";
        note = `[App] The user approved action ${action.id} (${action.summary}) but Meta returned an error: ${err.message}`;
      }
    } else {
      action.status = "rejected";
      note = `[App] The user REJECTED action ${action.id} (${action.summary}). It was not executed.`;
    }
    action.resolvedAt = new Date().toISOString();

    logChat(user, "system", `${approved ? "✅ Approved" : "✖ Rejected"}: ${action.summary}`);
    const out = await runAgent(user, req.token, [{ type: "text", text: `${note}\nBriefly confirm the outcome to the user and suggest the next step.` }]);
    logChat(user, "assistant", out.reply);
    await saveUser(user);
    return { status: 200, body: { reply: out.reply, pending: pendingList(user), actionStatus: action.status } };
  });
  res.status(result.status).json(result.body);
}

app.post("/api/actions/:id/approve", requireUser, requireAi, (req, res) => resolveAction(req, res, true));
app.post("/api/actions/:id/reject", requireUser, requireAi, (req, res) => resolveAction(req, res, false));

// ---------- Reports ----------
app.get("/api/reports", requireUser, (req, res) => res.json({ reports: req.user.reports || [] }));

app.post("/api/reports/run", requireUser, requireAi, async (req, res) => {
  const report = await withLock(req.user.id, () => runDailyReport(getUser(req.user.id)));
  res.json({ report });
});

// ---------- Errors ----------
app.use((err, req, res, _next) => {
  if (err instanceof GraphError) {
    if (err.code === 190) {
      const user = req.session.userId && getUser(req.session.userId);
      if (user) {
        user.tokenEnc = null;
        saveUser(user).catch(() => {});
      }
      return res.status(401).json({ error: "Your Facebook connection has expired. Please reconnect." });
    }
    return res.status(400).json({ error: `Meta: ${err.message}` });
  }
  if (err instanceof multer.MulterError) return res.status(400).json({ error: err.message });
  console.error(err);
  const status = err.status && err.status < 500 ? err.status : 500;
  res.status(status).json({ error: status === 500 ? "Something went wrong. Please try again." : err.message });
});

app.listen(config.port, () => {
  console.log(`AdPilot AI running at ${config.appUrl}`);
  startScheduler();
});
