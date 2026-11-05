// Turn raw Insights rows into compact numbers the agent and rules can reason about.

const num = (v) => (v === undefined || v === null || v === "" ? 0 : Number(v));

const RESULT_ACTIONS = {
  link_click: "linkClicks",
  lead: "leads",
  "onsite_conversion.lead_grouped": "leads",
  purchase: "purchases",
  "offsite_conversion.fb_pixel_purchase": "purchases",
  "onsite_conversion.messaging_conversation_started_7d": "messages",
  post_engagement: "engagements",
  page_engagement: "pageEngagements",
  video_view: "videoViews",
};

export function compactRow(r) {
  const results = {};
  for (const a of r.actions || []) {
    const key = RESULT_ACTIONS[a.action_type];
    if (key) results[key] = Math.max(results[key] || 0, num(a.value));
  }
  const roas = (r.purchase_roas || []).find((x) => x.action_type === "omni_purchase") || (r.purchase_roas || [])[0];
  return {
    campaign: r.campaign_name,
    campaignId: r.campaign_id,
    adset: r.adset_name,
    adsetId: r.adset_id,
    ad: r.ad_name,
    adId: r.ad_id,
    spend: num(r.spend),
    impressions: num(r.impressions),
    reach: num(r.reach),
    frequency: round(num(r.frequency)),
    clicks: num(r.clicks),
    linkClicks: num(r.inline_link_clicks),
    ctr: round(num(r.ctr)),
    linkCtr: round(num(r.inline_link_click_ctr)),
    cpc: round(num(r.cpc)),
    cpm: round(num(r.cpm)),
    results,
    roas: roas ? round(num(roas.value)) : null,
  };
}

export function totals(rows) {
  const t = rows.reduce(
    (acc, r) => {
      acc.spend += r.spend;
      acc.impressions += r.impressions;
      acc.clicks += r.clicks;
      acc.linkClicks += r.linkClicks;
      for (const [k, v] of Object.entries(r.results)) acc.results[k] = (acc.results[k] || 0) + v;
      return acc;
    },
    { spend: 0, impressions: 0, clicks: 0, linkClicks: 0, results: {} },
  );
  t.spend = round(t.spend);
  t.ctr = t.impressions ? round((t.clicks / t.impressions) * 100) : 0;
  t.cpm = t.impressions ? round((t.spend / t.impressions) * 1000) : 0;
  t.cpc = t.clicks ? round(t.spend / t.clicks) : 0;
  return t;
}

function round(n) {
  return Math.round(n * 100) / 100;
}
