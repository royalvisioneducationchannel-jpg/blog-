# PROJECT STATUS — Seller Toolkit

_Last updated: 2026-11-11_

## Current state

| Phase | Name | Status |
|---|---|---|
| — | Build pack saved to repository | ✅ Done |
| 00 | Connect, inspect and protect | ✅ **Complete** (one recommended follow-up: an off-server backup copy) |
| 01 | Audit tools, validate needs, define scope | ⏭️ **Next** |
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

- **Target verified:** `lightyellow-alligator-410734.hostingersite.com` is on the owner's Hostinger account. WordPress 7.1.4 on PHP 8.3.36. No customers, orders or WooCommerce.
- **Connections:** Hostinger API ✅. Dev-site WordPress connector `Seller_Toolkit_Dev` ✅ (verified it points at the dev site). Live-site connectors `Novalra` (store) and `Royal_law` are **excluded**. A cloud environment that reaches only the dev host ✅.
- **Inventory:** theme, 12 plugins, 12 pages, 9 tools, server limits, cron, email, caching, auto-update settings.
- **Backup R0 created and verified** in `/home/u56136xxxx/seller-toolkit-backups/2026-11-11-R0/`, outside the webroot, permissions 0700. Contents: full database plus all 18,644 files, with 0 missing, all 4 zip integrity checks OK, the 23 custom-code file hashes matching live, and SQL rows equal to live rows. Includes `RESTORE.txt` and `MANIFEST.txt`.
- **Staging protection R1 applied (dev only):** "Discourage search engines" is on; every page and REST response sends a `noindex, nofollow` meta tag and `X-Robots-Tag` header; the sitemap is disabled (404).
- **Regression after R1:** 12/12 routes OK with no site JS errors. A real Image Resizer run produced a 2000×2000 JPEG in a ZIP that downloaded and opened correctly.
- **Isolation confirmed:** no WooCommerce, gateway, SMTP or customer data on dev, and plugin/theme auto-updates are off.

## Decisions

- Working brand name: **Seller Toolkit** (final name to be confirmed before branding/domain launch).
- Spec source of truth: `docs/seller-toolkit/MASTER-PROMPT.md`.
- Only the `Seller_Toolkit_Dev` connector is used for site work. `Novalra` and `Royal_law` are never used.
- Novamira's write guardrail is respected: PHP and `.htaccess` changes go only through `wp-content/novamira-sandbox/`, with no bypass.
- Plugin/theme source is not committed while this repo is public. The server backup R0 and the sandbox version zips are the current source copies.

## Needs owner input

**Recommended soon (not blocking Phase 01):**
1. **Off-server backup copy:** download the R0 folder once through hPanel File Manager (`seller-toolkit-backups/2026-11-11-R0/`), or generate an hPanel backup. Keep it private: it contains password hashes and tokens.
2. **Make the GitHub repo private.** Then the plugin source can be versioned here.
3. **Elementor Pro vs "PRO Elements":** buy official Elementor Pro, or explicitly accept PRO Elements on dev. Needed before Phase 03.

**For Phase 01:**
4. Final brand name, business country, currency, licence budget, refund/cancellation rules.

## Blockers

- None for Phase 01.

## Not yet live / not configured

- remove.bg key (background removal disabled; the UI says so correctly)
- WooCommerce, subscriptions, payment gateway
- SMTP/sender domain, real server cron
- Dev-site password protection (noindex is not access control; needed before test customer data, Phase 06)
- `WP_ENVIRONMENT_TYPE` is still `production` on dev (set it to `staging` before Phase 07)

## Rollback points

| Point | What | Roll back |
|---|---|---|
| R0 | Baseline full backup, 2026-11-11 | `RESTORE.txt` in the backup folder (dev site only) |
| R1 | `blog_public` 1 → 0; added `wp-content/novamira-sandbox/seller-staging-noindex.php` | Re-tick visibility under Settings → Reading; delete or disable the sandbox file |

## Exact next step

**Phase 01: audit tools, validate needs and define the sellable scope.**
1. Test all 9 tools with valid, invalid and large files at 360/390/768/1440 px. Record features, processing location, errors, limits and mobile usability. Note from Phase 00: a resized JPEG came out larger than the PNG original (9.7 KB → 33.4 KB), so check compression honesty.
2. Write `TOOL-AUDIT.md` with keep / improve / merge / hide recommendations (no deletions).
3. Research competitors from first-party docs, with dated sources. Write 5 interview questions and a pilot checklist.
4. Write the draft catalog, plan-feature-limit matrix and cost worksheet (prices marked DRAFT).
5. Ask the owner for brand name, country, currency, licence budget and refund rules.
