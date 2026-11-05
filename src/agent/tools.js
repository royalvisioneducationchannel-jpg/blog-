// Tools the AI agent can call. Each maps to a Marketing API operation in ../meta/ads.js.
import crypto from "node:crypto";
import * as ads from "../meta/ads.js";
import { compactRow, totals } from "../meta/insights.js";

const str = (description, extra = {}) => ({ type: "string", description, ...extra });
const numberProp = (description) => ({ type: "number", description });

export const TOOL_DEFINITIONS = [
  {
    name: "get_account_overview",
    description:
      "Get the connected ad account (currency, status, spend) and all its campaigns with status and budgets. Call this first when you need to know what exists.",
    input_schema: { type: "object", properties: {} },
  },
  {
    name: "list_adsets",
    description: "List ad sets with status, budget, optimisation goal and targeting. Optionally filter by campaign.",
    input_schema: { type: "object", properties: { campaign_id: str("Campaign ID to filter by (optional)") } },
  },
  {
    name: "list_ads",
    description: "List ads with status and creative. Optionally filter by ad set.",
    input_schema: { type: "object", properties: { adset_id: str("Ad set ID to filter by (optional)") } },
  },
  {
    name: "get_insights",
    description:
      "Get performance metrics (spend, impressions, reach, frequency, clicks, CTR, CPC, CPM, results, ROAS). Use date_preset (today, yesterday, last_3d, last_7d, last_14d, last_30d, this_month, last_month, maximum) or since/until dates.",
    input_schema: {
      type: "object",
      properties: {
        level: str("Breakdown level", { enum: ["account", "campaign", "adset", "ad"] }),
        date_preset: str("Date preset, default last_7d"),
        since: str("Start date YYYY-MM-DD (use with until)"),
        until: str("End date YYYY-MM-DD (use with since)"),
        object_id: str("Limit to one campaign/ad set/ad ID (optional)"),
      },
      required: ["level"],
    },
  },
  {
    name: "search_locations",
    description:
      "Find Meta location keys for local targeting (cities, regions, countries). City keys are needed for city + radius targeting.",
    input_schema: {
      type: "object",
      properties: {
        query: str("Place name, e.g. 'Lahore'"),
        types: { type: "array", items: { type: "string", enum: ["city", "region", "country", "zip"] } },
      },
      required: ["query"],
    },
  },
  {
    name: "search_interests",
    description: "Find interest targeting IDs, e.g. 'fitness', 'online shopping'.",
    input_schema: { type: "object", properties: { query: str("Interest keyword") }, required: ["query"] },
  },
  {
    name: "list_custom_audiences",
    description: "List custom and lookalike audiences in the ad account with approximate sizes.",
    input_schema: { type: "object", properties: {} },
  },
  {
    name: "list_uploaded_images",
    description: "List creative images the user has uploaded in this app (with image_hash to use in ad creatives).",
    input_schema: { type: "object", properties: {} },
  },
  {
    name: "create_campaign",
    description:
      "Create a campaign (always created PAUSED). Give daily_budget only for campaign-level budget; otherwise set budgets on ad sets.",
    input_schema: {
      type: "object",
      properties: {
        name: str("Campaign name"),
        objective: str("Campaign objective", { enum: ads.OBJECTIVES }),
        daily_budget: numberProp("Optional campaign daily budget in the account currency (major units, e.g. 1500 for PKR 1,500)"),
        special_ad_categories: {
          type: "array",
          items: { type: "string", enum: ["HOUSING", "EMPLOYMENT", "CREDIT", "ISSUES_ELECTIONS_POLITICS", "FINANCIAL_PRODUCTS_SERVICES"] },
          description: "Required by Meta when the ad is about these topics; empty otherwise",
        },
      },
      required: ["name", "objective"],
    },
  },
  {
    name: "create_ad_set",
    description:
      "Create an ad set (always PAUSED) with budget, optimisation goal and targeting. For local audiences use cities (with radius) or custom_locations (lat/long + radius).",
    input_schema: {
      type: "object",
      properties: {
        name: str("Ad set name"),
        campaign_id: str("Parent campaign ID"),
        daily_budget: numberProp("Daily budget in account currency (major units). Omit if the campaign has a campaign budget."),
        optimization_goal: str("e.g. LINK_CLICKS, LANDING_PAGE_VIEWS, REACH, IMPRESSIONS, POST_ENGAGEMENT, LEAD_GENERATION, CONVERSATIONS, OFFSITE_CONVERSIONS"),
        billing_event: str("Usually IMPRESSIONS"),
        start_time: str("Optional ISO 8601 start time"),
        targeting: {
          type: "object",
          properties: {
            countries: { type: "array", items: { type: "string" }, description: "ISO country codes, e.g. ['PK']" },
            cities: {
              type: "array",
              items: {
                type: "object",
                properties: { key: { type: "string" }, radius: { type: "number" }, distance_unit: { type: "string", enum: ["kilometer", "mile"] } },
                required: ["key"],
              },
            },
            custom_locations: {
              type: "array",
              items: {
                type: "object",
                properties: {
                  latitude: { type: "number" },
                  longitude: { type: "number" },
                  radius: { type: "number" },
                  distance_unit: { type: "string", enum: ["kilometer", "mile"] },
                },
                required: ["latitude", "longitude"],
              },
            },
            age_min: { type: "integer" },
            age_max: { type: "integer" },
            genders: { type: "array", items: { type: "integer", enum: [1, 2] }, description: "1 = male, 2 = female; omit for all" },
            interests: {
              type: "array",
              items: { type: "object", properties: { id: { type: "string" }, name: { type: "string" } }, required: ["id"] },
            },
            custom_audience_ids: { type: "array", items: { type: "string" } },
            excluded_audience_ids: { type: "array", items: { type: "string" } },
            advantage_audience: { type: "boolean", description: "Let Meta expand the audience beyond these settings" },
          },
        },
      },
      required: ["name", "campaign_id", "targeting"],
    },
  },
  {
    name: "create_ad_creative",
    description:
      "Create an image link-ad creative on the connected Facebook Page. Write the copy yourself (primary text, headline, description) following Meta ad policies.",
    input_schema: {
      type: "object",
      properties: {
        name: str("Internal creative name"),
        image_hash: str("image_hash from list_uploaded_images"),
        link: str("Destination URL (website, product page, WhatsApp link, etc.)"),
        primary_text: str("Main ad text shown above the image"),
        headline: str("Headline shown under the image (short)"),
        description: str("Optional link description"),
        call_to_action: str("Button type", { enum: ads.CTA_TYPES }),
      },
      required: ["name", "image_hash", "link", "primary_text", "headline", "call_to_action"],
    },
  },
  {
    name: "create_ad",
    description: "Create an ad (always PAUSED) that links an ad creative to an ad set.",
    input_schema: {
      type: "object",
      properties: { name: str("Ad name"), adset_id: str("Ad set ID"), creative_id: str("Ad creative ID") },
      required: ["name", "adset_id", "creative_id"],
    },
  },
  {
    name: "set_status",
    description:
      "Turn a campaign, ad set or ad ON (ACTIVE) or OFF (PAUSED). Turning ON spends money and always waits for the user's approval button.",
    input_schema: {
      type: "object",
      properties: {
        object_id: str("Campaign, ad set or ad ID"),
        status: str("New status", { enum: ["ACTIVE", "PAUSED"] }),
        reason: str("Short reason shown to the user"),
      },
      required: ["object_id", "status", "reason"],
    },
  },
  {
    name: "update_daily_budget",
    description: "Change the daily budget of a campaign or ad set. Always waits for the user's approval button.",
    input_schema: {
      type: "object",
      properties: {
        object_id: str("Campaign or ad set ID"),
        new_daily_budget: numberProp("New daily budget in account currency (major units)"),
        reason: str("Short reason shown to the user"),
      },
      required: ["object_id", "new_daily_budget", "reason"],
    },
  },
  {
    name: "create_customer_list_audience",
    description:
      "Create a custom audience from customer emails/phones the user provided. Data is SHA-256 hashed before upload. Waits for approval.",
    input_schema: {
      type: "object",
      properties: {
        name: str("Audience name"),
        description: str("Optional description"),
        emails: { type: "array", items: { type: "string" } },
        phones: { type: "array", items: { type: "string" }, description: "With country code, e.g. 923001234567" },
      },
      required: ["name"],
    },
  },
  {
    name: "create_page_engagement_audience",
    description: "Create a custom audience of people who engaged with the connected Facebook Page.",
    input_schema: {
      type: "object",
      properties: { name: str("Audience name"), days: { type: "integer", description: "Look-back window in days (max 730), default 365" } },
      required: ["name"],
    },
  },
  {
    name: "create_lookalike_audience",
    description: "Create a lookalike audience from an existing custom audience. ratio 0.01 = top 1% most similar people in that country.",
    input_schema: {
      type: "object",
      properties: {
        name: str("Audience name"),
        origin_audience_id: str("Source custom audience ID"),
        country: str("ISO country code, e.g. PK"),
        ratio: { type: "number", description: "0.01 to 0.20" },
      },
      required: ["name", "origin_audience_id", "country"],
    },
  },
];

/** Actions that can spend money or upload customer data need an explicit click from the user. */
export function needsApproval(name, input) {
  if (name === "set_status") return input.status === "ACTIVE";
  return name === "update_daily_budget" || name === "create_customer_list_audience";
}

export function describeAction(name, input, currency) {
  switch (name) {
    case "set_status":
      return `${input.status === "ACTIVE" ? "Turn ON (start spending)" : "Pause"} ${input.object_id}: ${input.reason}`;
    case "update_daily_budget":
      return `Set daily budget of ${input.object_id} to ${currency} ${input.new_daily_budget}: ${input.reason}`;
    case "create_customer_list_audience":
      return `Upload ${input.emails?.length || 0} emails and ${input.phones?.length || 0} phone numbers (hashed) as audience "${input.name}"`;
    default:
      return `${name} ${JSON.stringify(input)}`;
  }
}

function requireSetup(ctx, { page = false } = {}) {
  if (!ctx.user.adAccountId) throw new Error("No ad account selected. Ask the user to pick one in the sidebar.");
  if (page && !ctx.user.pageId) throw new Error("No Facebook Page selected. Ask the user to pick one in the sidebar.");
}

/** Run a tool for real (after any approval). Returns a JSON-serialisable result. */
export async function executeTool(name, input, ctx) {
  const { token, user } = ctx;
  const accountId = user.adAccountId;
  const currency = user.currency || "USD";

  switch (name) {
    case "get_account_overview": {
      requireSetup(ctx);
      const [account, campaigns] = await Promise.all([ads.getAdAccount(token, accountId), ads.listCampaigns(token, accountId)]);
      return {
        account: { ...account, amount_spent: ads.fromMinor(account.amount_spent, currency) },
        campaigns: campaigns.map((c) => ({
          ...c,
          daily_budget: ads.fromMinor(c.daily_budget, currency),
          lifetime_budget: ads.fromMinor(c.lifetime_budget, currency),
        })),
      };
    }
    case "list_adsets": {
      requireSetup(ctx);
      const sets = await ads.listAdSets(token, accountId, input.campaign_id);
      return sets.map((s) => ({ ...s, daily_budget: ads.fromMinor(s.daily_budget, currency), lifetime_budget: ads.fromMinor(s.lifetime_budget, currency) }));
    }
    case "list_ads":
      requireSetup(ctx);
      return ads.listAds(token, accountId, input.adset_id);
    case "get_insights": {
      requireSetup(ctx);
      const rows = (
        await ads.getInsights(token, accountId, {
          level: input.level,
          datePreset: input.date_preset || "last_7d",
          since: input.since,
          until: input.until,
          objectId: input.object_id,
        })
      ).map(compactRow);
      return { currency, totals: totals(rows), rows };
    }
    case "search_locations":
      return ads.searchLocations(token, input.query, input.types);
    case "search_interests":
      return ads.searchInterests(token, input.query);
    case "list_custom_audiences":
      requireSetup(ctx);
      return ads.listCustomAudiences(token, accountId);
    case "list_uploaded_images":
      return (user.assets || []).filter((a) => a.accountId === accountId).map(({ name, hash, url, uploadedAt }) => ({ name, image_hash: hash, url, uploadedAt }));
    case "create_campaign":
      requireSetup(ctx);
      return ads.createCampaign(token, accountId, {
        name: input.name,
        objective: input.objective,
        dailyBudget: input.daily_budget,
        currency,
        specialAdCategories: input.special_ad_categories || [],
      });
    case "create_ad_set": {
      requireSetup(ctx);
      const t = input.targeting || {};
      return ads.createAdSet(token, accountId, {
        name: input.name,
        campaignId: input.campaign_id,
        dailyBudget: input.daily_budget,
        currency,
        optimizationGoal: input.optimization_goal,
        billingEvent: input.billing_event,
        startTime: input.start_time,
        pageId: user.pageId,
        targeting: {
          countries: t.countries,
          cities: t.cities,
          customLocations: t.custom_locations,
          ageMin: t.age_min,
          ageMax: t.age_max,
          genders: t.genders,
          interests: t.interests,
          customAudienceIds: t.custom_audience_ids,
          excludedAudienceIds: t.excluded_audience_ids,
          advantageAudience: t.advantage_audience,
        },
      });
    }
    case "create_ad_creative":
      requireSetup(ctx, { page: true });
      return ads.createAdCreative(token, accountId, {
        name: input.name,
        pageId: user.pageId,
        imageHash: input.image_hash,
        link: input.link,
        message: input.primary_text,
        headline: input.headline,
        description: input.description,
        cta: input.call_to_action,
      });
    case "create_ad":
      requireSetup(ctx);
      return ads.createAd(token, accountId, { name: input.name, adsetId: input.adset_id, creativeId: input.creative_id });
    case "set_status":
      await ads.setStatus(token, input.object_id, input.status);
      return ads.getObject(token, input.object_id, "id,name,status,effective_status");
    case "update_daily_budget": {
      await ads.setDailyBudget(token, input.object_id, input.new_daily_budget, currency);
      const obj = await ads.getObject(token, input.object_id, "id,name,daily_budget");
      return { ...obj, daily_budget: ads.fromMinor(obj.daily_budget, currency), currency };
    }
    case "create_customer_list_audience":
      requireSetup(ctx);
      return ads.createCustomerListAudience(token, accountId, input);
    case "create_page_engagement_audience":
      requireSetup(ctx, { page: true });
      return ads.createPageEngagementAudience(token, accountId, { name: input.name, pageId: user.pageId, days: input.days });
    case "create_lookalike_audience":
      requireSetup(ctx);
      return ads.createLookalikeAudience(token, accountId, {
        name: input.name,
        originAudienceId: input.origin_audience_id,
        country: input.country,
        ratio: Math.min(Math.max(Number(input.ratio || 0.01), 0.01), 0.2),
      });
    default:
      throw new Error(`Unknown tool: ${name}`);
  }
}

/** Called by the agent loop: either queue the action for approval or run it now. */
export async function handleToolCall(name, input, ctx) {
  if (needsApproval(name, input)) {
    const id = crypto.randomUUID();
    const summary = describeAction(name, input, ctx.user.currency || "USD");
    ctx.user.pendingActions ||= {};
    ctx.user.pendingActions[id] = { id, tool: name, input, summary, status: "pending", createdAt: new Date().toISOString() };
    ctx.newPending.push({ id, summary });
    return {
      status: "pending_approval",
      action_id: id,
      summary,
      note: "Not executed yet. Tell the user what this will do and ask them to press Approve or Reject on the card in the chat. Do not call this tool again for the same action.",
    };
  }
  return executeTool(name, input, ctx);
}
