import "dotenv/config";

const env = process.env;

export const config = {
  port: Number(env.PORT || 3000),
  appUrl: (env.APP_URL || `http://localhost:${env.PORT || 3000}`).replace(/\/$/, ""),
  isProduction: env.NODE_ENV === "production",
  sessionSecret: env.SESSION_SECRET || "",
  encryptionKey: env.ENCRYPTION_KEY || "",
  dataDir: env.DATA_DIR || "./data",

  meta: {
    appId: env.META_APP_ID || "",
    appSecret: env.META_APP_SECRET || "",
    graphVersion: env.META_GRAPH_VERSION || "v23.0",
    // Permissions requested at Facebook Login. Advanced access needs Meta App Review.
    scopes: [
      "ads_management",
      "ads_read",
      "business_management",
      "pages_show_list",
      "pages_read_engagement",
      "pages_manage_ads",
    ],
  },

  ai: {
    model: env.AI_MODEL || "claude-opus-5-5",
    effort: env.AI_EFFORT || "medium",
    maxToolIterations: 15,
  },

  reports: {
    cron: env.REPORT_CRON || "0 9 * * *",
    timezone: env.REPORT_TZ || "Asia/Karachi",
  },
};

/** Settings that are missing; shown in the UI so setup problems are obvious. */
export function missingConfig() {
  const missing = [];
  if (!config.meta.appId) missing.push("META_APP_ID");
  if (!config.meta.appSecret) missing.push("META_APP_SECRET");
  if (!env.ANTHROPIC_API_KEY && !env.ANTHROPIC_AUTH_TOKEN) missing.push("ANTHROPIC_API_KEY");
  if (!config.sessionSecret) missing.push("SESSION_SECRET");
  if (!config.encryptionKey) missing.push("ENCRYPTION_KEY");
  return missing;
}
