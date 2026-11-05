// AdPilot AI front-end (no framework).
const $ = (sel) => document.querySelector(sel);
const els = {
  alert: $("#alert"),
  connectView: $("#connect-view"),
  appView: $("#app-view"),
  setupWarning: $("#setup-warning"),
  userName: $("#user-name"),
  logout: $("#logout-btn"),
  accountSelect: $("#account-select"),
  pageSelect: $("#page-select"),
  saveSelect: $("#save-select"),
  settingsForm: $("#settings-form"),
  runReport: $("#run-report"),
  reports: $("#reports"),
  contextLine: $("#context-line"),
  messages: $("#messages"),
  quick: $("#quick"),
  form: $("#chat-form"),
  input: $("#chat-input"),
  send: $("#send-btn"),
  fileInput: $("#file-input"),
  attachments: $("#attachments"),
  reset: $("#reset-chat"),
};

let state = { user: null, attachments: [], busy: false };

// ---------- helpers ----------
async function api(path, options = {}) {
  const res = await fetch(path, {
    ...options,
    headers: options.body instanceof FormData ? {} : { "Content-Type": "application/json" },
  });
  const data = await res.json().catch(() => ({}));
  if (res.status === 401 && path !== "/api/status") {
    showAlert(data.error || "Please reconnect Facebook.");
    setTimeout(() => location.reload(), 1500);
  }
  if (!res.ok) throw new Error(data.error || `Request failed (${res.status})`);
  return data;
}

function showAlert(msg) {
  els.alert.textContent = msg;
  els.alert.hidden = !msg;
  if (msg) setTimeout(() => (els.alert.hidden = true), 8000);
}

const escapeHtml = (s) => String(s).replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]);

function inline(s) {
  return s
    .replace(/`([^`]+)`/g, "<code>$1</code>")
    .replace(/\*\*([^*]+)\*\*/g, "<strong>$1</strong>")
    .replace(/(^|[\s(])\*([^*\n]+)\*(?=[\s).,!?]|$)/g, "$1<em>$2</em>")
    .replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');
}

/** Small, safe Markdown renderer: escapes first, then formats headings, lists, tables and inline marks. */
function renderMarkdown(text) {
  const lines = escapeHtml(text).split("\n");
  let html = "";
  let list = null;
  const closeList = () => {
    if (list) html += `</${list}>`;
    list = null;
  };

  for (let i = 0; i < lines.length; i++) {
    const line = lines[i];
    if (/^\s*\|.*\|\s*$/.test(line) && /^\s*\|?[\s:-]+\|[\s|:-]*$/.test(lines[i + 1] || "")) {
      closeList();
      const cells = (l) => l.trim().replace(/^\||\|$/g, "").split("|").map((c) => inline(c.trim()));
      html += `<div class="table-wrap"><table><thead><tr>${cells(line).map((c) => `<th>${c}</th>`).join("")}</tr></thead><tbody>`;
      i += 2;
      while (i < lines.length && /^\s*\|.*\|\s*$/.test(lines[i])) {
        html += `<tr>${cells(lines[i]).map((c) => `<td>${c}</td>`).join("")}</tr>`;
        i++;
      }
      i--;
      html += "</tbody></table></div>";
      continue;
    }
    const h = line.match(/^#{1,4}\s+(.*)/);
    const ul = line.match(/^\s*[-*•]\s+(.*)/);
    const ol = line.match(/^\s*\d+[.)]\s+(.*)/);
    if (h) {
      closeList();
      html += `<h4>${inline(h[1])}</h4>`;
    } else if (ul || ol) {
      const type = ul ? "ul" : "ol";
      if (list !== type) {
        closeList();
        html += `<${type}>`;
        list = type;
      }
      html += `<li>${inline((ul || ol)[1])}</li>`;
    } else if (!line.trim()) {
      closeList();
    } else {
      closeList();
      html += `<p>${inline(line)}</p>`;
    }
  }
  closeList();
  return html;
}

// ---------- chat rendering ----------
function addMessage(role, text) {
  els.messages.querySelector(".empty")?.remove();
  const div = document.createElement("div");
  div.className = `msg ${role}`;
  if (role === "assistant") {
    div.classList.add("md");
    div.innerHTML = renderMarkdown(text);
  } else {
    div.textContent = text;
  }
  els.messages.appendChild(div);
  els.messages.scrollTop = els.messages.scrollHeight;
  return div;
}

function showEmpty() {
  els.messages.innerHTML = `<div class="empty"><h3>Salam! 👋</h3><p>Ask anything about your ads, upload an ad image to launch it, or pick a quick action below.</p></div>`;
}

function renderPending(pending) {
  els.messages.querySelectorAll(".action-card").forEach((c) => c.remove());
  for (const p of pending || []) {
    const card = document.createElement("div");
    card.className = "action-card";
    card.innerHTML = `<div class="label">Needs your approval</div><p></p><div class="action-buttons">
      <button class="btn btn-success" data-act="approve" type="button">Approve</button>
      <button class="btn btn-danger" data-act="reject" type="button">Reject</button></div>`;
    card.querySelector("p").textContent = p.summary;
    card.querySelectorAll("button").forEach((b) => b.addEventListener("click", () => resolveAction(p.id, b.dataset.act, card)));
    els.messages.appendChild(card);
  }
  els.messages.scrollTop = els.messages.scrollHeight;
}

function typing() {
  const div = document.createElement("div");
  div.className = "msg assistant typing";
  div.innerHTML = '<span class="typing-dots" aria-label="AdPilot is thinking"><span></span><span></span><span></span></span>';
  els.messages.appendChild(div);
  els.messages.scrollTop = els.messages.scrollHeight;
  return div;
}

function setBusy(busy) {
  state.busy = busy;
  els.send.disabled = busy;
  els.quick.querySelectorAll("button").forEach((b) => (b.disabled = busy));
}

async function sendMessage(text) {
  if (state.busy) return;
  const attachments = state.attachments.map((a) => a.hash);
  if (!text.trim() && !attachments.length) return;
  addMessage("user", text.trim() || `📎 ${attachments.length} image(s)`);
  els.input.value = "";
  autosize();
  state.attachments = [];
  renderAttachments();
  setBusy(true);
  const t = typing();
  try {
    const data = await api("/api/chat", { method: "POST", body: JSON.stringify({ message: text, attachments }) });
    t.remove();
    addMessage("assistant", data.reply);
    renderPending(data.pending);
  } catch (err) {
    t.remove();
    addMessage("system", `⚠️ ${err.message}`);
  } finally {
    setBusy(false);
    els.input.focus();
  }
}

async function resolveAction(id, act, card) {
  card.querySelectorAll("button").forEach((b) => (b.disabled = true));
  setBusy(true);
  const t = typing();
  try {
    const data = await api(`/api/actions/${id}/${act}`, { method: "POST" });
    t.remove();
    card.remove();
    addMessage("system", `${act === "approve" ? "✅ Approved" : "✖ Rejected"}`);
    addMessage("assistant", data.reply);
    renderPending(data.pending);
  } catch (err) {
    t.remove();
    card.querySelectorAll("button").forEach((b) => (b.disabled = false));
    addMessage("system", `⚠️ ${err.message}`);
  } finally {
    setBusy(false);
  }
}

// ---------- attachments ----------
function renderAttachments() {
  els.attachments.innerHTML = "";
  for (const a of state.attachments) {
    const chip = document.createElement("div");
    chip.className = "chip";
    chip.innerHTML = `<img alt=""><span></span><button type="button" aria-label="Remove image">×</button>`;
    chip.querySelector("img").src = a.preview;
    chip.querySelector("span").textContent = a.name;
    chip.querySelector("button").addEventListener("click", () => {
      state.attachments = state.attachments.filter((x) => x !== a);
      renderAttachments();
    });
    els.attachments.appendChild(chip);
  }
}

els.fileInput.addEventListener("change", async () => {
  const file = els.fileInput.files[0];
  els.fileInput.value = "";
  if (!file) return;
  if (!state.user?.adAccountId) return showAlert("Select and save an ad account first.");
  const body = new FormData();
  body.append("image", file);
  const pending = { name: `Uploading ${file.name}…`, preview: URL.createObjectURL(file) };
  state.attachments.push(pending);
  renderAttachments();
  try {
    const { asset } = await api("/api/upload", { method: "POST", body });
    Object.assign(pending, { hash: asset.hash, name: asset.name });
  } catch (err) {
    state.attachments = state.attachments.filter((x) => x !== pending);
    showAlert(err.message);
  }
  renderAttachments();
});

// ---------- composer ----------
function autosize() {
  els.input.style.height = "auto";
  els.input.style.height = `${Math.min(els.input.scrollHeight, 160)}px`;
}
els.input.addEventListener("input", autosize);
els.input.addEventListener("keydown", (e) => {
  if (e.key === "Enter" && !e.shiftKey) {
    e.preventDefault();
    els.form.requestSubmit();
  }
});
els.form.addEventListener("submit", (e) => {
  e.preventDefault();
  if (state.attachments.some((a) => !a.hash)) return showAlert("Please wait for the image upload to finish.");
  sendMessage(els.input.value);
});
els.quick.addEventListener("click", (e) => {
  const btn = e.target.closest("button[data-prompt]");
  if (btn) sendMessage(btn.dataset.prompt);
});
els.reset.addEventListener("click", async () => {
  if (!confirm("Start a new chat? The current conversation will be cleared.")) return;
  await api("/api/chat/reset", { method: "POST" });
  showEmpty();
});

// ---------- sidebar ----------
function updateContextLine() {
  const u = state.user;
  els.contextLine.textContent =
    u.adAccountId && u.pageId ? `${u.adAccountName} · ${u.currency} · Page: ${u.pageName}` : "Select an ad account and Page in the sidebar to begin.";
}

async function loadAccounts() {
  try {
    const { adAccounts, pages } = await api("/api/accounts");
    const opt = (v, t, sel) => `<option value="${escapeHtml(v)}"${sel ? " selected" : ""}>${escapeHtml(t)}</option>`;
    els.accountSelect.innerHTML =
      opt("", adAccounts.length ? "Select ad account" : "No ad accounts found") +
      adAccounts.map((a) => opt(a.id, `${a.name} (${a.currency})`, a.id === state.user.adAccountId)).join("");
    els.pageSelect.innerHTML =
      opt("", pages.length ? "Select Page" : "No Pages found") + pages.map((p) => opt(p.id, p.name, p.id === state.user.pageId)).join("");
  } catch (err) {
    showAlert(err.message);
  }
}

els.saveSelect.addEventListener("click", async () => {
  els.saveSelect.disabled = true;
  try {
    const { user } = await api("/api/select", {
      method: "POST",
      body: JSON.stringify({ adAccountId: els.accountSelect.value, pageId: els.pageSelect.value }),
    });
    state.user = user;
    updateContextLine();
    showAlert("");
  } catch (err) {
    showAlert(err.message);
  } finally {
    els.saveSelect.disabled = false;
  }
});

function fillSettings(s) {
  const f = els.settingsForm;
  f.autopilot.checked = !!s.autopilot;
  for (const k of ["maxCpm", "maxCpc", "minCtr", "maxFrequency", "minSpendForPause", "reportLanguage"]) f[k].value = s[k] ?? "";
}

els.settingsForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const f = els.settingsForm;
  const body = { autopilot: f.autopilot.checked };
  for (const k of ["maxCpm", "maxCpc", "minCtr", "maxFrequency", "minSpendForPause", "reportLanguage"]) body[k] = f[k].value;
  try {
    const { settings } = await api("/api/settings", { method: "POST", body: JSON.stringify(body) });
    state.user.settings = settings;
    fillSettings(settings);
    showAlert("");
    f.querySelector("button[type=submit]").textContent = "Saved ✓";
    setTimeout(() => (f.querySelector("button[type=submit]").textContent = "Save rules"), 1500);
  } catch (err) {
    showAlert(err.message);
  }
});

function renderReports(reports) {
  els.reports.innerHTML = reports.length ? "" : '<p class="hint">No reports yet. They run every morning automatically.</p>';
  for (const r of reports.slice(0, 15)) {
    const d = document.createElement("details");
    d.className = "report";
    d.innerHTML = `<summary></summary><div class="md"></div>`;
    d.querySelector("summary").textContent = `${r.date} · ${r.currency || ""} ${r.totals?.spend ?? 0} spent · ${r.findings?.length || 0} alerts`;
    d.querySelector(".md").innerHTML = renderMarkdown(r.summary || "");
    els.reports.appendChild(d);
  }
}

els.runReport.addEventListener("click", async () => {
  els.runReport.disabled = true;
  els.runReport.textContent = "Generating…";
  try {
    await api("/api/reports/run", { method: "POST" });
    const { reports } = await api("/api/reports");
    renderReports(reports);
  } catch (err) {
    showAlert(err.message);
  } finally {
    els.runReport.disabled = false;
    els.runReport.textContent = "Generate report now";
  }
});

els.logout.addEventListener("click", async () => {
  await api("/auth/logout", { method: "POST" });
  location.href = "/";
});

// ---------- boot ----------
async function boot() {
  const err = new URLSearchParams(location.search).get("error");
  if (err) {
    showAlert(err);
    history.replaceState(null, "", "/");
  }
  const status = await api("/api/status");
  if (status.missingConfig.length) {
    els.setupWarning.hidden = false;
    els.setupWarning.innerHTML = `Setup needed: add ${status.missingConfig.map((k) => `<code>${escapeHtml(k)}</code>`).join(", ")} to your <code>.env</code> file and restart the server.`;
  }
  if (!status.connected) {
    els.connectView.hidden = false;
    return;
  }

  state.user = status.user;
  els.appView.hidden = false;
  els.logout.hidden = false;
  els.userName.textContent = status.user.name;
  updateContextLine();
  fillSettings(status.user.settings);
  loadAccounts();

  const [chat, reports] = await Promise.all([api("/api/chat"), api("/api/reports")]);
  if (chat.messages.length) chat.messages.forEach((m) => addMessage(m.role, m.text));
  else showEmpty();
  renderPending(chat.pending);
  renderReports(reports.reports);
}

boot().catch((e) => showAlert(e.message));
