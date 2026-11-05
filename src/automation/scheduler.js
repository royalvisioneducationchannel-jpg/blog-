// Daily job: pull yesterday's results, apply rules, optionally auto-pause, and write an AI report.
import crypto from "node:crypto";
import cron from "node-cron";
import { config } from "../config.js";
import { allUsers, saveUser, userToken } from "../store.js";
import { getInsights, setStatus } from "../meta/ads.js";
import { compactRow, totals } from "../meta/insights.js";
import { evaluateRules } from "./rules.js";
import { writeReportSummary } from "../agent/agent.js";

const MAX_AUTO_PAUSES_PER_RUN = 5;
const MAX_REPORTS_KEPT = 60;

export async function runDailyReport(user) {
  const token = userToken(user);
  if (!token) throw new Error("Facebook connection expired. Please reconnect.");
  if (!user.adAccountId) throw new Error("No ad account selected.");

  const rows = (await getInsights(token, user.adAccountId, { level: "ad", datePreset: "yesterday" })).map(compactRow);
  const findings = evaluateRules(rows, user.settings);
  const actionsTaken = [];

  if (user.settings.autopilot) {
    const toPause = [...new Map(findings.filter((f) => f.autoPause).map((f) => [f.adId, f])).values()].slice(0, MAX_AUTO_PAUSES_PER_RUN);
    for (const f of toPause) {
      try {
        await setStatus(token, f.adId, "PAUSED");
        actionsTaken.push({ action: "paused", adId: f.adId, ad: f.ad, reason: f.detail });
      } catch (err) {
        actionsTaken.push({ action: "pause_failed", adId: f.adId, ad: f.ad, error: err.message });
      }
    }
  }

  const date = new Date(Date.now() - 86400000).toISOString().slice(0, 10);
  const sum = totals(rows);
  let summary;
  try {
    summary = await writeReportSummary({
      accountName: user.adAccountName,
      currency: user.currency,
      date,
      totals: sum,
      rows,
      findings,
      actionsTaken,
      language: user.settings.reportLanguage || "English",
    });
  } catch (err) {
    summary = `AI summary failed: ${err.message}`;
  }

  const report = {
    id: crypto.randomUUID(),
    createdAt: new Date().toISOString(),
    date,
    accountId: user.adAccountId,
    currency: user.currency,
    totals: sum,
    findings,
    actionsTaken,
    summary,
  };
  user.reports = [report, ...(user.reports || [])].slice(0, MAX_REPORTS_KEPT);
  await saveUser(user);
  return report;
}

export function startScheduler() {
  if (!cron.validate(config.reports.cron)) {
    console.warn(`Invalid REPORT_CRON "${config.reports.cron}"; daily reports disabled.`);
    return;
  }
  cron.schedule(
    config.reports.cron,
    async () => {
      for (const user of allUsers()) {
        if (!user.adAccountId || !user.tokenEnc) continue;
        try {
          await runDailyReport(user);
          console.log(`Daily report done for ${user.id}`);
        } catch (err) {
          console.error(`Daily report failed for ${user.id}: ${err.message}`);
        }
      }
    },
    { timezone: config.reports.timezone },
  );
  console.log(`Daily reports scheduled: "${config.reports.cron}" (${config.reports.timezone})`);
}
