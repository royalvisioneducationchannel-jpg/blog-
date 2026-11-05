// Small JSON-file store. Good for a single-server MVP; swap for Postgres/Supabase in production.
import fs from "node:fs/promises";
import path from "node:path";
import crypto from "node:crypto";
import { config } from "./config.js";

const DB_FILE = path.join(config.dataDir, "db.json");
export const UPLOAD_DIR = path.join(config.dataDir, "uploads");

let db = { users: {} };
let writeChain = Promise.resolve();

export const DEFAULT_SETTINGS = {
  autopilot: false, // when on, the daily job pauses clearly failing ads without asking
  maxCpm: 0, // in account currency; 0 = rule off
  maxCpc: 0,
  minCtr: 0.8, // percent
  maxFrequency: 3.5,
  minSpendForPause: 0, // spend (account currency) an ad must reach before auto-pause; 0 = use 3x maxCpc or skip
  reportLanguage: "Roman Urdu",
};

export async function initStore() {
  await fs.mkdir(UPLOAD_DIR, { recursive: true });
  try {
    db = JSON.parse(await fs.readFile(DB_FILE, "utf8"));
    db.users ||= {};
  } catch (err) {
    if (err.code !== "ENOENT") throw err;
    await persist();
  }
}

function persist() {
  const snapshot = JSON.stringify(db, null, 2);
  writeChain = writeChain.then(async () => {
    const tmp = `${DB_FILE}.tmp`;
    await fs.writeFile(tmp, snapshot, { mode: 0o600 });
    await fs.rename(tmp, DB_FILE);
  });
  return writeChain;
}

export function getUser(id) {
  return db.users[id] || null;
}

export function allUsers() {
  return Object.values(db.users);
}

export async function upsertUser(id, patch) {
  const existing = db.users[id] || {
    id,
    settings: { ...DEFAULT_SETTINGS },
    conversation: [],
    pendingActions: {},
    assets: [],
    reports: [],
    createdAt: new Date().toISOString(),
  };
  db.users[id] = { ...existing, ...patch, settings: { ...existing.settings, ...(patch.settings || {}) } };
  await persist();
  return db.users[id];
}

export async function saveUser(user) {
  db.users[user.id] = user;
  await persist();
}

// ---- Access-token encryption at rest (AES-256-GCM) ----
function key() {
  return crypto.createHash("sha256").update(config.encryptionKey).digest();
}

export function encrypt(plain) {
  const iv = crypto.randomBytes(12);
  const cipher = crypto.createCipheriv("aes-256-gcm", key(), iv);
  const data = Buffer.concat([cipher.update(plain, "utf8"), cipher.final()]);
  return [iv, cipher.getAuthTag(), data].map((b) => b.toString("base64")).join(".");
}

export function decrypt(blob) {
  const [iv, tag, data] = blob.split(".").map((s) => Buffer.from(s, "base64"));
  const decipher = crypto.createDecipheriv("aes-256-gcm", key(), iv);
  decipher.setAuthTag(tag);
  return Buffer.concat([decipher.update(data), decipher.final()]).toString("utf8");
}

export function userToken(user) {
  if (!user?.tokenEnc) return null;
  if (user.tokenExpiresAt && Date.parse(user.tokenExpiresAt) < Date.now()) return null;
  return decrypt(user.tokenEnc);
}
