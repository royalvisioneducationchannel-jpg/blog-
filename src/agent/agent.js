// Claude-powered ads agent: a manual tool-use loop over the Marketing API tools.
import Anthropic from "@anthropic-ai/sdk";
import { config } from "../config.js";
import { TOOL_DEFINITIONS, handleToolCall } from "./tools.js";

const client = new Anthropic();
const MAX_TOOL_RESULT_CHARS = 20000;

const SYSTEM_PROMPT = `You are AdPilot, an expert Facebook & Instagram ads manager working inside a web app. The user has connected their Meta ad account and Facebook Page, and you operate them through the tools provided (Meta Marketing API).

What you help with:
- Writing ad creatives: primary text, headlines, descriptions and call-to-action choices, in several variants when useful. Keep copy specific to the product, benefit-led, and compliant with Meta advertising policies (no personal-attribute claims like "Are you overweight?", no misleading or exaggerated promises, no before/after health claims). When the user wants an image, describe a clear image brief they can produce or upload.
- Launching ads from an uploaded image: campaign -> ad set (budget, optimisation goal, targeting) -> ad creative -> ad. Everything is created PAUSED. After creating, summarise what was built and offer to turn it on.
- Analysing performance: pull insights, explain spend, CTR, CPC, CPM, frequency, results and ROAS in plain words, and give concrete recommendations (pause, scale, refresh creative, change audience or placement).
- Audiences: customer-list custom audiences, Page-engagement audiences, lookalikes, and local audiences (city + radius, or a lat/long pin + radius via search_locations).

How you work:
- Use tools to check real data before making claims about the account; never invent IDs or numbers.
- Budgets are in the account's currency in normal units (e.g. 1500 means PKR 1,500 for a PKR account).
- Turning anything ON, changing budgets, and uploading customer data are queued for the user's approval in the app. When a tool returns pending_approval, explain what will happen and ask the user to press Approve. Never claim it is done until you receive the approval result.
- Before launching, confirm key choices you would otherwise be guessing: destination link, daily budget, location, and objective. If the user gave them, go ahead.
- If an API call fails, read the error, fix the input if you can, and otherwise tell the user what Meta reported and what they can do.
- Reply in the same language and script the user writes in (Roman Urdu, Urdu or English). Keep answers short and scannable: short paragraphs, bullet points, and simple tables for metrics.
- Messages that start with [App] are written by the app, not the user. They carry context (selected account, page, date, uploads) and approval results.`;

function contextBlock(user) {
  const lines = [
    `[App] Context for this message (written by the app):`,
    `- Today: ${new Date().toISOString().slice(0, 10)}`,
    `- Ad account: ${user.adAccountName || "not selected"} (${user.adAccountId || "-"}), currency ${user.currency || "unknown"}, timezone ${user.timezone || "unknown"}`,
    `- Facebook Page: ${user.pageName || "not selected"} (${user.pageId || "-"})`,
    `- Uploaded creative images for this account: ${(user.assets || []).filter((a) => a.accountId === user.adAccountId).length}`,
  ];
  const pending = Object.values(user.pendingActions || {}).filter((a) => a.status === "pending");
  if (pending.length) lines.push(`- Actions waiting for approval: ${pending.map((p) => p.summary).join("; ")}`);
  return lines.join("\n");
}

function truncate(text) {
  return text.length > MAX_TOOL_RESULT_CHARS ? `${text.slice(0, MAX_TOOL_RESULT_CHARS)}\n...[truncated]` : text;
}

/**
 * Run one user turn through the agent.
 * @param {object} user   stored user record (conversation is appended in place)
 * @param {string} token  Meta access token
 * @param {Array}  content user content blocks (text and optional images)
 */
export async function runAgent(user, token, content) {
  user.conversation ||= [];
  const messages = user.conversation;
  const startLength = messages.length;
  const ctx = { user, token, newPending: [] };
  const replyParts = [];

  messages.push({ role: "user", content: [{ type: "text", text: contextBlock(user) }, ...content] });

  try {
    for (let i = 0; i < config.ai.maxToolIterations; i++) {
      const lastIteration = i === config.ai.maxToolIterations - 1;
      const response = await client.beta.messages.create({
        model: config.ai.model,
        max_tokens: 16000,
        betas: ["server-side-fallback-2026-07-01"],
        fallbacks: "default",
        thinking: { type: "adaptive" },
        output_config: { effort: config.ai.effort },
        cache_control: { type: "ephemeral" },
        system: SYSTEM_PROMPT,
        tools: TOOL_DEFINITIONS,
        tool_choice: lastIteration ? { type: "none" } : { type: "auto" },
        messages,
      });

      if (response.stop_reason === "refusal") {
        messages.length = startLength; // drop this unanswered turn; history stays a clean prefix
        dropPending(user, ctx.newPending);
        return {
          reply: "Sorry, I can't help with that request. Please rephrase it or try a different request.",
          pending: [],
        };
      }

      messages.push({ role: "assistant", content: response.content });
      for (const block of response.content) if (block.type === "text" && block.text.trim()) replyParts.push(block.text.trim());

      if (response.stop_reason === "pause_turn") continue;

      const toolUses = response.content.filter((b) => b.type === "tool_use");
      if (!toolUses.length) break; // end_turn or max_tokens with plain text

      const results = await Promise.all(
        toolUses.map(async (tu) => {
          if (response.stop_reason === "max_tokens") {
            return { type: "tool_result", tool_use_id: tu.id, is_error: true, content: "Tool input was cut off. Please try again with a shorter input." };
          }
          try {
            const out = await handleToolCall(tu.name, tu.input || {}, ctx);
            return { type: "tool_result", tool_use_id: tu.id, content: truncate(JSON.stringify(out)) };
          } catch (err) {
            const detail = { error: err.message, code: err.code, subcode: err.subcode, title: err.title };
            return { type: "tool_result", tool_use_id: tu.id, is_error: true, content: JSON.stringify(detail) };
          }
        }),
      );
      messages.push({ role: "user", content: results });
    }
  } catch (err) {
    messages.length = startLength;
    dropPending(user, ctx.newPending);
    throw err;
  }

  return { reply: replyParts.join("\n\n") || "Done.", pending: ctx.newPending };
}

function dropPending(user, list) {
  for (const p of list) delete user.pendingActions[p.id];
}

/** One-shot summary for the daily report (no tools). */
export async function writeReportSummary({ accountName, currency, date, totals, rows, findings, actionsTaken, language }) {
  const response = await client.beta.messages.create({
    model: config.ai.model,
    max_tokens: 4000,
    betas: ["server-side-fallback-2026-07-01"],
    fallbacks: "default",
    thinking: { type: "adaptive" },
    output_config: { effort: "low" },
    system:
      "You write daily Facebook ads performance reports for small business owners. Be concise and practical: a 2-3 line summary, a short metrics table, what went well, what needs attention, and 3 concrete next steps. Use only the numbers provided.",
    messages: [
      {
        role: "user",
        content: `Write the report in ${language}.\n\nData (JSON):\n${JSON.stringify({ accountName, currency, date, totals, rows: rows.slice(0, 40), findings, actionsTaken })}`,
      },
    ],
  });
  if (response.stop_reason === "refusal") return "AI summary unavailable for this report.";
  return response.content.filter((b) => b.type === "text").map((b) => b.text).join("\n").trim();
}
