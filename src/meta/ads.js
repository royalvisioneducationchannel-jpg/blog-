// Marketing API operations used by the agent, the UI and the daily automation.
import crypto from "node:crypto";
import { graph, graphList } from "./graph.js";

const act = (id) => (String(id).startsWith("act_") ? id : `act_${id}`);

// Currencies Meta stores without a minor unit (offset 1). All others use offset 100.
const ZERO_DECIMAL = new Set(["CLP", "COP", "CRC", "HUF", "ISK", "IDR", "JPY", "KRW", "PYG", "TWD", "VND"]);
export const toMinor = (amount, currency) => Math.round(Number(amount) * (ZERO_DECIMAL.has(currency) ? 1 : 100));
export const fromMinor = (minor, currency) =>
  minor == null || minor === "" ? null : Number(minor) / (ZERO_DECIMAL.has(currency) ? 1 : 100);

export const OBJECTIVES = [
  "OUTCOME_AWARENESS",
  "OUTCOME_TRAFFIC",
  "OUTCOME_ENGAGEMENT",
  "OUTCOME_LEADS",
  "OUTCOME_SALES",
  "OUTCOME_APP_PROMOTION",
];

export const CTA_TYPES = [
  "LEARN_MORE",
  "SHOP_NOW",
  "SIGN_UP",
  "CONTACT_US",
  "BOOK_NOW",
  "GET_OFFER",
  "ORDER_NOW",
  "DOWNLOAD",
  "APPLY_NOW",
  "SEND_MESSAGE",
  "WHATSAPP_MESSAGE",
  "CALL_NOW",
];

// ---------- Account & Pages ----------
export const getMe = (token) => graph(token, "me", { params: { fields: "id,name" } });

export const listAdAccounts = (token) =>
  graphList(token, "me/adaccounts", { fields: "id,name,account_status,currency,timezone_name,amount_spent" });

export const listPages = (token) => graphList(token, "me/accounts", { fields: "id,name,category" });

export const getAdAccount = (token, accountId) =>
  graph(token, act(accountId), {
    params: { fields: "id,name,account_status,currency,timezone_name,amount_spent,spend_cap,balance" },
  });

// ---------- Read ----------
export const listCampaigns = (token, accountId) =>
  graphList(token, `${act(accountId)}/campaigns`, {
    fields: "id,name,objective,status,effective_status,daily_budget,lifetime_budget,created_time",
  });

export const listAdSets = (token, accountId, campaignId) =>
  graphList(token, campaignId ? `${campaignId}/adsets` : `${act(accountId)}/adsets`, {
    fields: "id,name,campaign_id,status,effective_status,daily_budget,lifetime_budget,optimization_goal,billing_event,targeting",
  });

export const listAds = (token, accountId, adsetId) =>
  graphList(token, adsetId ? `${adsetId}/ads` : `${act(accountId)}/ads`, {
    fields: "id,name,adset_id,campaign_id,status,effective_status,creative{id,name}",
  });

export const INSIGHT_FIELDS = [
  "campaign_id",
  "campaign_name",
  "adset_id",
  "adset_name",
  "ad_id",
  "ad_name",
  "spend",
  "impressions",
  "reach",
  "frequency",
  "clicks",
  "inline_link_clicks",
  "ctr",
  "inline_link_click_ctr",
  "cpc",
  "cpm",
  "actions",
  "cost_per_action_type",
  "purchase_roas",
].join(",");

/**
 * @param {object} opts level: account|campaign|adset|ad; datePreset (e.g. yesterday, last_7d) or since/until (YYYY-MM-DD)
 */
export function getInsights(token, accountId, { level = "ad", datePreset = "last_7d", since, until, objectId } = {}) {
  const params = { level, fields: INSIGHT_FIELDS };
  if (since && until) params.time_range = { since, until };
  else params.date_preset = datePreset;
  return graphList(token, `${objectId || act(accountId)}/insights`, params);
}

export const searchLocations = (token, q, types = ["city", "region", "country"]) =>
  graph(token, "search", { params: { type: "adgeolocation", q, location_types: types, limit: 10 } }).then((r) => r.data);

export const searchInterests = (token, q) =>
  graph(token, "search", { params: { type: "adinterest", q, limit: 15 } }).then((r) => r.data);

export const listCustomAudiences = (token, accountId) =>
  graphList(token, `${act(accountId)}/customaudiences`, {
    fields: "id,name,subtype,approximate_count_lower_bound,approximate_count_upper_bound,operation_status",
  });

// ---------- Create (everything is created PAUSED so nothing spends until approved) ----------
export function createCampaign(token, accountId, { name, objective, dailyBudget, currency, specialAdCategories = [] }) {
  const params = {
    name,
    objective,
    status: "PAUSED",
    special_ad_categories: specialAdCategories,
  };
  if (dailyBudget) {
    // Campaign budget (Advantage+ campaign budget)
    params.daily_budget = toMinor(dailyBudget, currency);
    params.bid_strategy = "LOWEST_COST_WITHOUT_CAP";
  } else {
    params.is_adset_budget_sharing_enabled = false;
  }
  return graph(token, `${act(accountId)}/campaigns`, { method: "POST", params });
}

/**
 * targeting input:
 * { countries: ["PK"], cities: [{key, radius, distance_unit}], customLocations: [{latitude, longitude, radius, distance_unit}],
 *   ageMin, ageMax, genders: [1|2], interests: [{id,name}], customAudienceIds: [], excludedAudienceIds: [] }
 */
export function buildTargeting(t = {}) {
  const geo = {};
  if (t.countries?.length) geo.countries = t.countries;
  if (t.cities?.length)
    geo.cities = t.cities.map((c) => ({ key: String(c.key), radius: c.radius ?? 10, distance_unit: c.distance_unit || "kilometer" }));
  if (t.customLocations?.length)
    geo.custom_locations = t.customLocations.map((l) => ({
      latitude: l.latitude,
      longitude: l.longitude,
      radius: l.radius ?? 5,
      distance_unit: l.distance_unit || "kilometer",
    }));
  if (!Object.keys(geo).length) geo.countries = ["PK"];

  const targeting = {
    geo_locations: geo,
    age_min: t.ageMin ?? 18,
    age_max: t.ageMax ?? 65,
    targeting_automation: { advantage_audience: t.advantageAudience ? 1 : 0 },
  };
  if (t.genders?.length) targeting.genders = t.genders;
  if (t.interests?.length) targeting.flexible_spec = [{ interests: t.interests.map((i) => ({ id: String(i.id), name: i.name })) }];
  if (t.customAudienceIds?.length) targeting.custom_audiences = t.customAudienceIds.map((id) => ({ id: String(id) }));
  if (t.excludedAudienceIds?.length) targeting.excluded_custom_audiences = t.excludedAudienceIds.map((id) => ({ id: String(id) }));
  return targeting;
}

export function createAdSet(token, accountId, opts) {
  const { name, campaignId, dailyBudget, currency, optimizationGoal = "LINK_CLICKS", billingEvent = "IMPRESSIONS", targeting, startTime, pageId } =
    opts;
  const params = {
    name,
    campaign_id: campaignId,
    status: "PAUSED",
    optimization_goal: optimizationGoal,
    billing_event: billingEvent,
    targeting: buildTargeting(targeting),
    start_time: startTime,
  };
  if (dailyBudget) {
    params.daily_budget = toMinor(dailyBudget, currency);
    params.bid_strategy = "LOWEST_COST_WITHOUT_CAP";
  }
  if (pageId && ["LEAD_GENERATION", "CONVERSATIONS", "PAGE_LIKES", "POST_ENGAGEMENT"].includes(optimizationGoal)) {
    params.promoted_object = { page_id: pageId };
  }
  return graph(token, `${act(accountId)}/adsets`, { method: "POST", params });
}

/** Upload an image (base64) to the ad account's image library. Returns { hash, url }. */
export async function uploadImage(token, accountId, base64) {
  const res = await graph(token, `${act(accountId)}/adimages`, { method: "POST", params: { bytes: base64 } });
  const img = Object.values(res.images || {})[0];
  if (!img?.hash) throw new Error("Meta did not return an image hash.");
  return { hash: img.hash, url: img.url };
}

export function createAdCreative(token, accountId, { name, pageId, imageHash, link, message, headline, description, cta = "LEARN_MORE" }) {
  return graph(token, `${act(accountId)}/adcreatives`, {
    method: "POST",
    params: {
      name,
      object_story_spec: {
        page_id: pageId,
        link_data: {
          image_hash: imageHash,
          link,
          message,
          name: headline,
          description,
          call_to_action: { type: cta, value: { link } },
        },
      },
    },
  });
}

export const createAd = (token, accountId, { name, adsetId, creativeId }) =>
  graph(token, `${act(accountId)}/ads`, {
    method: "POST",
    params: { name, adset_id: adsetId, creative: { creative_id: creativeId }, status: "PAUSED" },
  });

// ---------- Update ----------
export const setStatus = (token, objectId, status) => graph(token, objectId, { method: "POST", params: { status } });

export const setDailyBudget = (token, objectId, dailyBudget, currency) =>
  graph(token, objectId, { method: "POST", params: { daily_budget: toMinor(dailyBudget, currency) } });

export const getObject = (token, objectId, fields = "id,name,status,effective_status,daily_budget") =>
  graph(token, objectId, { params: { fields } });

// ---------- Audiences ----------
const sha256 = (v) => crypto.createHash("sha256").update(v).digest("hex");
const normEmail = (e) => String(e).trim().toLowerCase();
const normPhone = (p) => String(p).replace(/\D/g, "").replace(/^0+/, "");

/** Customer-list custom audience. Emails/phones are normalised and SHA-256 hashed before upload. */
export async function createCustomerListAudience(token, accountId, { name, description, emails = [], phones = [] }) {
  const audience = await graph(token, `${act(accountId)}/customaudiences`, {
    method: "POST",
    params: { name, description, subtype: "CUSTOM", customer_file_source: "USER_PROVIDED_ONLY" },
  });

  const upload = async (schema, values) => {
    for (let i = 0; i < values.length; i += 10000) {
      const chunk = values.slice(i, i + 10000).map((v) => [sha256(v)]);
      await graph(token, `${audience.id}/users`, { method: "POST", params: { payload: { schema: [schema], data: chunk } } });
    }
  };
  const cleanEmails = [...new Set(emails.map(normEmail).filter((e) => e.includes("@")))];
  const cleanPhones = [...new Set(phones.map(normPhone).filter((p) => p.length >= 8))];
  if (cleanEmails.length) await upload("EMAIL", cleanEmails);
  if (cleanPhones.length) await upload("PHONE", cleanPhones);
  return { id: audience.id, uploadedEmails: cleanEmails.length, uploadedPhones: cleanPhones.length };
}

/** People who engaged with the Facebook Page in the last `days` days. */
export const createPageEngagementAudience = (token, accountId, { name, pageId, days = 365 }) =>
  graph(token, `${act(accountId)}/customaudiences`, {
    method: "POST",
    params: {
      name,
      subtype: "ENGAGEMENT",
      prefill: true,
      rule: {
        inclusions: {
          operator: "or",
          rules: [
            {
              event_sources: [{ id: pageId, type: "page" }],
              retention_seconds: Math.min(days, 730) * 86400,
              filter: { operator: "and", filters: [{ field: "event", operator: "eq", value: "page_engaged" }] },
            },
          ],
        },
      },
    },
  });

export const createLookalikeAudience = (token, accountId, { name, originAudienceId, country = "PK", ratio = 0.01 }) =>
  graph(token, `${act(accountId)}/customaudiences`, {
    method: "POST",
    params: {
      name,
      subtype: "LOOKALIKE",
      origin_audience_id: originAudienceId,
      lookalike_spec: { type: "similarity", ratio, country },
    },
  });
