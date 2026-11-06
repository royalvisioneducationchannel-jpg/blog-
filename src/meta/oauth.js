// Facebook Login (OAuth 2.0) for connecting a user's ad accounts and Pages.
import { config } from "../config.js";
import { graph } from "./graph.js";

export const redirectUri = () => `${config.appUrl}/auth/facebook/callback`;

export function loginUrl(state) {
  const url = new URL(`https://www.facebook.com/${config.meta.graphVersion}/dialog/oauth`);
  url.searchParams.set("client_id", config.meta.appId);
  url.searchParams.set("redirect_uri", redirectUri());
  url.searchParams.set("state", state);
  if (config.meta.loginConfigId) url.searchParams.set("config_id", config.meta.loginConfigId);
  else url.searchParams.set("scope", config.meta.scopes.join(","));
  url.searchParams.set("response_type", "code");
  return url.toString();
}

/** Exchange the login code for a short-lived token, then upgrade it to a long-lived (~60 day) token. */
export async function exchangeCode(code) {
  const short = await graph(null, "oauth/access_token", {
    params: {
      client_id: config.meta.appId,
      client_secret: config.meta.appSecret,
      redirect_uri: redirectUri(),
      code,
    },
  });
  const long = await graph(null, "oauth/access_token", {
    params: {
      grant_type: "fb_exchange_token",
      client_id: config.meta.appId,
      client_secret: config.meta.appSecret,
      fb_exchange_token: short.access_token,
    },
  });
  const expiresIn = Number(long.expires_in || 60 * 24 * 3600);
  return {
    accessToken: long.access_token,
    expiresAt: new Date(Date.now() + expiresIn * 1000).toISOString(),
  };
}
