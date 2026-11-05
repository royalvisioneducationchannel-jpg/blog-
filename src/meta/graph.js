// Minimal Meta Graph API client (Marketing API runs on the Graph API).
import crypto from "node:crypto";
import { config } from "../config.js";

export class GraphError extends Error {
  constructor(error, status) {
    super(error?.error_user_msg || error?.message || `Graph API error (${status})`);
    this.name = "GraphError";
    this.status = status;
    this.code = error?.code;
    this.subcode = error?.error_subcode;
    this.title = error?.error_user_title;
    this.fbtraceId = error?.fbtrace_id;
  }
}

const base = () => `https://graph.facebook.com/${config.meta.graphVersion}`;

function appSecretProof(token) {
  return crypto.createHmac("sha256", config.meta.appSecret).update(token).digest("hex");
}

function encode(params) {
  const out = {};
  for (const [k, v] of Object.entries(params)) {
    if (v === undefined || v === null) continue;
    out[k] = typeof v === "object" ? JSON.stringify(v) : String(v);
  }
  return out;
}

/**
 * Call the Graph API. GET params go in the query string; POST/DELETE params are form-encoded.
 */
export async function graph(token, path, { method = "GET", params = {} } = {}) {
  const url = new URL(`${base()}/${path.replace(/^\//, "")}`);
  const all = encode({ ...params, access_token: token, appsecret_proof: token ? appSecretProof(token) : undefined });

  let init = { method };
  if (method === "GET") {
    for (const [k, v] of Object.entries(all)) url.searchParams.set(k, v);
  } else {
    init.body = new URLSearchParams(all);
  }

  const res = await fetch(url, init);
  const text = await res.text();
  let json;
  try {
    json = JSON.parse(text);
  } catch {
    throw new GraphError({ message: `Unexpected response from Meta: ${text.slice(0, 200)}` }, res.status);
  }
  if (!res.ok || json.error) throw new GraphError(json.error, res.status);
  return json;
}

/** Follow `paging.next` up to `maxPages` and return the merged `data` array. */
export async function graphList(token, path, params = {}, maxPages = 5) {
  const first = await graph(token, path, { params: { limit: 100, ...params } });
  let items = first.data || [];
  let next = first.paging?.next;
  for (let i = 1; next && i < maxPages; i++) {
    const res = await fetch(next);
    const json = await res.json();
    if (json.error) throw new GraphError(json.error, res.status);
    items = items.concat(json.data || []);
    next = json.paging?.next;
  }
  return items;
}
