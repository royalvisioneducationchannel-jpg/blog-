// Quick offline checks for the pure logic (no Meta or Claude calls).
import assert from "node:assert/strict";
import { evaluateRules } from "../src/automation/rules.js";
import { toMinor, fromMinor, buildTargeting } from "../src/meta/ads.js";
import { compactRow, totals } from "../src/meta/insights.js";
import { needsApproval, TOOL_DEFINITIONS } from "../src/agent/tools.js";

// Currency conversion
assert.equal(toMinor(1500, "PKR"), 150000);
assert.equal(toMinor(1000, "JPY"), 1000);
assert.equal(fromMinor("150000", "PKR"), 1500);

// Targeting
const t = buildTargeting({ cities: [{ key: "2211096", radius: 10 }], ageMin: 21 });
assert.deepEqual(t.geo_locations.cities, [{ key: "2211096", radius: 10, distance_unit: "kilometer" }]);
assert.equal(t.age_min, 21);
assert.deepEqual(buildTargeting({}).geo_locations, { countries: ["PK"] });

// Insights
const row = compactRow({ ad_id: "1", ad_name: "A", spend: "500", impressions: "5000", clicks: "10", ctr: "0.2", cpm: "100", cpc: "50", frequency: "4.1",
  actions: [{ action_type: "link_click", value: "8" }] });
assert.equal(row.results.linkClicks, 8);
assert.equal(totals([row]).cpm, 100);

// Rules
const findings = evaluateRules([row], { maxCpm: 80, maxCpc: 0, minCtr: 1, maxFrequency: 3.5, minSpendForPause: 300 });
const issues = findings.map((f) => f.issue).sort();
assert.deepEqual(issues, ["Ad fatigue", "High CPM", "Low CTR"]);
assert.equal(findings.find((f) => f.issue === "Low CTR").autoPause, true);

// Approval policy
assert.equal(needsApproval("set_status", { status: "ACTIVE" }), true);
assert.equal(needsApproval("set_status", { status: "PAUSED" }), false);
assert.equal(needsApproval("update_daily_budget", {}), true);
assert.equal(needsApproval("create_campaign", {}), false);

// Tool names are unique and valid
const names = TOOL_DEFINITIONS.map((d) => d.name);
assert.equal(new Set(names).size, names.length);
names.forEach((n) => assert.match(n, /^[a-zA-Z0-9_-]{1,64}$/));

console.log("All checks passed.");
