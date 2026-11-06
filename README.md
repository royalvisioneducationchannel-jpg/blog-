# AdPilot AI: Facebook Ads chatbot

An AI chatbot that connects to a user's **Facebook ad account and Page** and runs their ads from a chat:

- **Connect**: Facebook Login (OAuth). The user picks an ad account and a Page.
- **Create creatives**: the AI writes primary text, headlines and CTAs (several variants).
- **Run an existing creative**: the user uploads an image and says "run this ad". The AI builds campaign → ad set → creative → ad.
- **Optimise**: the AI reads insights and suggests what to pause or scale: high CPM/CPC, low CTR, ad fatigue.
- **Audiences**: customer-list custom audiences (hashed), Page-engagement audiences, lookalikes, and local audiences (city + radius, or a map pin + radius).
- **Daily report**: every morning it pulls yesterday's numbers, applies your rules, optionally auto-pauses failing ads (Autopilot), and writes an AI summary in Roman Urdu, Urdu or English.

**Safety by default**: everything is created **PAUSED**. Turning an ad **ON**, changing a **budget**, and uploading **customer data** always show an **Approve / Reject** card. Nothing spends money without a click.

## How it works

```
Browser chat (public/)  ──►  Express API (src/server.js)
                                 │
                                 ├─ Claude agent (src/agent/agent.js) ── tools (src/agent/tools.js)
                                 │                                          │
                                 │                                          ▼
                                 ├─ Meta Marketing API client (src/meta/*.js) ──► graph.facebook.com
                                 │
                                 └─ Daily scheduler + rules (src/automation/*.js)
```

The agent is Claude (`claude-opus-5-5`) using tool use. Each tool maps to one Marketing API call (list campaigns, get insights, create ad set, set status, and so on). Tools that spend money are queued as pending actions and run only after the user approves.

## Setup

### 1. Create a Meta app

1. Go to <https://developers.facebook.com/apps> → **Create app** → type **Business**.
2. Add the **Facebook Login** and **Marketing API** products.
3. In Facebook Login → Settings, add this to **Valid OAuth Redirect URIs**: `http://localhost:3000/auth/facebook/callback` (use your real domain in production).
4. Copy the **App ID** and **App Secret**.
5. Permissions used: `ads_management`, `ads_read`, `business_management`, `pages_show_list`, `pages_read_engagement`, `pages_manage_ads`.
   - In **development mode** these work for app admins, developers and testers.
   - For **other users** you need Meta **App Review** (Advanced Access) and Business Verification.

### 2. Get a Claude API key

Create one at <https://console.anthropic.com>.

### 3. Configure and run

```bash
cp .env.example .env
```

Fill in `META_APP_ID`, `META_APP_SECRET`, `ANTHROPIC_API_KEY`, and set `SESSION_SECRET` and `ENCRYPTION_KEY` to long random strings. You can generate one with:

```bash
openssl rand -hex 32
```

Then install and start:

```bash
npm install
```

```bash
npm start
```

Open <http://localhost:3000>, click **Connect Facebook Ads**, choose your ad account and Page, and start chatting.

### Checks

```bash
npm run check
```

This runs offline checks for currency conversion, targeting, insights parsing, rules and the approval policy.

## Example prompts

- "Mera account overview do aur last 7 days ki performance batao."
- (upload image) "Is image se ad banao. Link: https://mystore.pk/sale, budget 1500 daily, Karachi 15 km, age 20-40."
- "Kaun se ads ki CPM high hai? Unhe band karo."
- "Meri Page engagers ki audience bana kar 1% lookalike banao (PK)."
- "Ad set X ka budget 2000 kar do."

## Configuration

| Variable | Purpose |
|---|---|
| `APP_URL` | Public URL; the OAuth redirect is `${APP_URL}/auth/facebook/callback` |
| `META_GRAPH_VERSION` | Graph API version (default `v23.0`) |
| `META_LOGIN_CONFIG_ID` | Configuration ID from Facebook Login for Business (Business-type apps); when set it replaces the `scope` list |
| `AI_MODEL` / `AI_EFFORT` | Claude model and effort (`low`, `medium`, `high`) |
| `REPORT_CRON` / `REPORT_TZ` | Daily report schedule (default 9:00 Asia/Karachi) |
| `DATA_DIR` | Where the JSON database and uploaded images are stored |

Automation thresholds (max CPM/CPC, min CTR, max frequency, min spend before auto-pause, Autopilot on/off, report language) are set per user in the sidebar.

## Production notes / next steps

- **Storage**: `data/db.json` is fine for one server. Move to Postgres or Supabase for multiple users or instances. Access tokens are already encrypted at rest (AES-256-GCM).
- **Sessions**: the default `express-session` memory store resets on restart. Use a persistent store (Redis or Postgres) in production, and run behind HTTPS with `NODE_ENV=production`.
- **Tokens**: long-lived user tokens last about 60 days, after which the user must reconnect. For agencies, use a System User token from Business Manager.
- **AI image generation**: today the AI writes copy and an image brief, and the user uploads the image. An image-generation provider can be added as one more tool.
- **MCP**: the tool layer in `src/agent/tools.js` is the same shape an MCP server exposes, so it can be published as an MCP server, or a hosted Meta Ads MCP can be connected, without changing the agent.
- **Meta API changes**: Marketing API fields change between Graph versions. If Meta rejects a field, the agent shows Meta's error message. Update `src/meta/ads.js` and `META_GRAPH_VERSION` together.
