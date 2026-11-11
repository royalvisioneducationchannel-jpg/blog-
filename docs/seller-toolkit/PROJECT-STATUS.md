# PROJECT STATUS — Seller Toolkit

_Last updated: 2026-11-11_

## Current state

| Phase | Name | Status |
|---|---|---|
| — | Build pack saved to repository | ✅ Done |
| 00 | Connect, inspect and protect | 🟡 **Inspection done. Waiting on owner for backup and dev-site connector.** |
| 01 | Audit tools, validate needs, define scope | Not started (blocked by Phase 00 items below) |
| 02 | Design system and prototypes | Not started |
| 03 | WordPress and Elementor foundation | Not started |
| 04 | Homepage, directory and sales pages | Not started |
| 05 | Polish tools and advanced workflows | Not started |
| 06 | Customer portal, workspaces, access control | Not started |
| 07 | Checkout, payments, subscription lifecycle | Not started |
| 08 | Seller Desk operations workspace | Not started |
| 09 | Platform CRM and renewal communications | Not started |
| 10 | Paid providers, usage limits, spending controls | Not started |
| 11 | Content, trust, SEO, AdSense readiness | Not started |
| 12 | Security, reliability, full journey QA | Not started |
| 13 | Final domain, activation, handover | Not started |

## Phase 00: completed work (evidence in `PHASE-00-INVENTORY.md`)

- Verified the dev target `lightyellow-alligator-410734.hostingersite.com` belongs to the connected Hostinger account. It is a valid WordPress 7.1.4 install with no customers, orders or WooCommerce.
- Identified that both connected WordPress MCPs point at **live** sites (`Novalra` = royalvisiontraders.com store, `Royal_law` = royalvisionlaw.com). Excluded both from this project.
- Inventoried the theme, 12 plugins, 12 pages and all 11 known tool routes, plus the homepage. **All return HTTP 200.** The browser smoke test at 390 px and 1440 px found no site JS errors and no horizontal overflow.
- Inventoried 9 tools from the plugin source (seller-tools 1.2.1): processing location, limits, and background-remover service state (`ready:false`).
- Checked indexing, SSL, cron, email, caching, database size and plan. Found staging is **not** protected from indexing.
- Created a dedicated cloud environment that can reach only the dev host, for testing.
- Recorded rollback point R0 (no site changes made).

## Decisions

- Working brand name: **Seller Toolkit** (final name to be confirmed before branding/domain launch).
- Source of truth for the spec: `docs/seller-toolkit/MASTER-PROMPT.md`.
- The live-site connectors (`Novalra`, `Royal_law`) are never used for this project.
- Plugin/theme source is not committed while this repo is public.

## Needs owner input (blocking)

1. **Full backup (hPanel):** Websites → `lightyellow-alligator-410734.hostingersite.com` → Backups → generate a new backup, then download **files** and **database** once to confirm they open. Send back only the date/time and confirmation, no files or passwords.
2. **Connect the dev site to Claude:** claude.ai → https://claude.ai/customize/connectors → add a custom connector with URL
   `https://lightyellow-alligator-410734.hostingersite.com/wp-json/mcp/novamira-oauth`, named e.g. "Seller Toolkit Dev". Approve the login in the dev site's WP admin. **Start a new session** afterwards (connectors load at session start).
3. **Repository visibility:** `royalvisioneducationchannel-jpg/blog-` is **public**. Recommend making it **private** (GitHub → Settings → General → Danger Zone → Change visibility), so the plugin source and business plan can be stored safely.
4. **Elementor Pro licence:** "PRO Elements" (an unofficial GPL redistribution) is installed instead of licensed Elementor Pro. Decide: buy official Elementor Pro, or explicitly accept PRO Elements on dev.

## Needs owner input (non-blocking, Phase 01)

- Final brand name, business country, currency, licence budget, refund/cancellation rules.

## Blockers

- No verified backup yet, so no site changes are allowed.
- No WordPress-level access to the dev site yet (Hostinger API is read-only for files and has no backup/settings operations).

## Not yet live / not configured

- remove.bg key (background removal disabled, correctly reported in UI)
- WooCommerce, subscriptions, payment gateway
- SMTP/sender domain, real server cron
- Staging noindex/access protection

## Rollback points

- **R0, 2026-11-11:** pre-change state, documented in `PHASE-00-INVENTORY.md`. Restorable once the hPanel backup exists. Restore the dev website only.

## Exact next step

When items 1 and 2 are done, in a **new session**:
1. Record the backup date and its verification in this file.
2. Through the dev-site connector, read Site Health (PHP version, memory, upload/execution limits), `home`/`siteurl`, and `blog_public`.
3. With R0 restorable, apply staging protection on dev only: "Discourage search engines" plus an `X-Robots-Tag: noindex` header. Optionally add password protection. Record it as rollback point R1.
4. Copy the plugin and theme source into a private location (private repo if approved) with checksums.
5. Then close Phase 00 and start Phase 01 (TOOL-AUDIT.md).
