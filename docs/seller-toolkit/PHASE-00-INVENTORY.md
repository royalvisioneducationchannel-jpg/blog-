# Phase 00 — Environment Inventory, Connections & Protection

_Inspected: 2026-11-11. Read-only, then backup R0, then staging protection R1 on the **dev site only**. No live site was touched._
_Sensitive identifiers are masked because this repository is currently **public**._

## 1. Verified development target

| Check | Result | Evidence |
|---|---|---|
| Dev URL | `https://lightyellow-alligator-410734.hostingersite.com/` | Hostinger `hosting_websites_list` |
| Belongs to connected Hostinger account | ✅ Yes, same hosting account and order as the owner's other sites | Account `u56136xxxx`, order `…4050` |
| Website type | WordPress (addon website), enabled | Hostinger API |
| Created on Hostinger | 2026-11-10 | Hostinger API |
| WordPress install valid | ✅ `is_valid: true`, no core updates pending | Hostinger WP API |
| WordPress version | 7.1.4 | Page `generator` meta |
| Site title | Seller Toolkit | REST index |
| Real customers/orders on dev | None found: WooCommerce is not installed, 1 user (`admin`), 1 post (`hello-world`) | Plugin list, REST |
| Other sites on the same account | `royalvisiontraders.com` (**LIVE store, WooCommerce with real orders**), `royalvisionlaw.com` (live) | Hostinger API. **Not to be touched by this project.** |

Hosting plan: `hostinger_business_v5` (active). Database: 1 MySQL DB, **14 MB used of 3,072 MB max**.

**Server limits (read through the dev-site connector, 2026-11-11):**

| Setting | Value |
|---|---|
| PHP | 8.3.36 (LiteSpeed SAPI) |
| PHP memory_limit | 2048M (WP_MEMORY_LIMIT 40M, WP_MAX_MEMORY_LIMIT 2048M) |
| max_execution_time | **30 s** (matters for server-side batch jobs and provider calls) |
| upload_max_filesize / post_max_size | 2048M / 2048M |
| max_input_vars | 5000 |
| MySQL / MariaDB | 11.8.9, 40 tables, prefix `wp_` |
| PHP extensions | gd, imagick, zip, curl, mbstring, intl, openssl, exif, fileinfo (no `sodium` listed) |
| Disabled functions | exec, shell_exec, system, passthru, proc_open, popen (so no `mysqldump`/CLI) |
| `home` / `siteurl` | both `https://…` ✅ |
| WP environment type | `production` (should become `staging` before any subscription plugin is installed, Phase 07) |
| Timezone | UTC (+00:00) |
| Permalinks | `/%postname%/` |
| WP-Cron | enabled, 18 scheduled events, no `DISABLE_WP_CRON` |
| Auto-updates | Plugins **off**, themes **off**, WordPress core major **on** |

Account disk/inode quota is not exposed to PHP (the free-space figure PHP reports is the whole server's), so check it in hPanel before storing customer files (Phase 10).

## 2. Connection status

| Connection | Status | Points to | Use for this project |
|---|---|---|---|
| Hostinger API (MCP) | ✅ Connected | Whole hosting account | Read inventory, list/read files (text only, secrets refused), plugin/theme lists. **No shared-hosting backup operation exists in this API.** |
| WordPress MCP `Novalra` | ✅ Connected | **royalvisiontraders.com (LIVE store)** | ❌ **Must not be used.** Wrong site. |
| WordPress MCP `Royal_law` | ✅ Connected | royalvisionlaw.com (live) | ❌ Wrong site. |
| WordPress MCP `Seller_Toolkit_Dev` | ✅ Connected 2026-11-11 | **Dev site.** Verified: `home` = dev URL, theme `seller-toolkit` | **The only WordPress connector used for this project.** Novamira guardrail: PHP and `.htaccess` writes are allowed only in `wp-content/novamira-sandbox/`. |
| Cloud session network → dev site | ✅ Enabled 2026-11-11 | New personal environment `blog- (Seller Toolkit dev)` allowing only the dev host | HTTP + browser testing |

## 3. Theme & plugins (dev site)

**Active theme:** `seller-toolkit` 1.0.0 (custom; files: `header.php`, `footer.php`, `front-page.php`, `page.php`, `index.php`, `functions.php`, `style.css`, `ui.js`). Inactive: Hostinger AI theme, Twenty Twenty-Three/Four/Five.

| Plugin | Version | Status | Note |
|---|---|---|---|
| **seller-tools** (Seller Toolkit Tools) | 1.2.1 | active | **The product.** Custom code; exists only on this server. |
| Elementor | 4.4.0 | active | Free core. Installed, but **no page currently uses Elementor** (0 Elementor elements rendered). |
| **PRO Elements** | 4.3.2 | active | ⚠️ **Not official Elementor Pro.** It is a third-party GPL redistribution of the Pro code, with no Elementor license, support or official updates. Also one minor version behind Elementor core. See license checklist. |
| Novamira | 1.12.7 | active | MCP/AI-agent bridge. Update available. Gives full server control to connected agents, so treat it as a sensitive admin tool. |
| Hostinger Tools | 3.0.81 | active | Update available |
| Hostinger Hub (easy onboarding) | 3.0.5 | active | Update available |
| Hostinger AI | 3.1.8 | active | |
| Hostinger Reach | 1.8.9 | active | Loads a third-party script (`cdn-reach.hostinger.com/js/embed.js`) on every page, which matters for the Phase 11 consent work |
| Hostinger Preview Domain ×2, Smart Auto Updates ×2 | 1.4.0 / 1.0.10 | inactive | Duplicate inactive copies. Cleanup candidates **only with approval**. |

No known vulnerabilities reported by Hostinger for any installed plugin or theme. **No WooCommerce, subscriptions, payment gateway or SMTP/email plugin is installed.** This keeps the dev site isolated from billing and messaging.

## 4. Pages & routes (all tested 2026-11-11)

All return **HTTP 200** over HTTPS (served via Hostinger CDN `hcdn`, `DYNAMIC` = not cached).

| Route | Page ID | Renders | Tool engine |
|---|---|---|---|
| `/` | 9 | "Everyday tools for online sellers." + demo profit calculator + tool list | theme `front-page.php` + `[seller_tool demo]` |
| `/tools/` | 10 | "All tools" directory | theme |
| `/tools/product-image-tools/` | 29 | Combined studio (starts in resize mode) | `seller_image_studio` |
| `/tools/image-resizer/` | 12 | Studio · Marketplace resize | studio |
| `/tools/image-compressor/` | 13 | Studio · Compress & convert | studio |
| `/tools/square-maker/` | 25 | Studio · Square maker | studio |
| `/tools/bulk-resizer/` | 26 | Studio · Bulk & ZIP | studio |
| `/tools/watermark/` | 27 | Studio · Watermark | studio |
| `/tools/background-remover/` | 28 | Studio · Remove background | studio + REST `seller-tools/v1/background` |
| `/tools/profit-calculator/` | 11 | Profit calculator | `[seller_tool id="profit-calculator"]` |
| `/tools/invoice-generator/` | 14 | Invoice generator (print/PDF) | `[seller_tool]` |
| `/tools/utm-builder/` | 15 | UTM link builder | `[seller_tool]` |

Not linked from the homepage: `/tools/product-image-tools/` (reachable via `/tools/` and the studio).

**Browser smoke test** (Chromium, 390 px and 1440 px, all 12 routes): no JavaScript errors from the site's own code; tool forms initialise (the `inert` attribute is removed by JS); **no horizontal overflow**. The only failed request was Hostinger Reach's external script, which this test sandbox blocks. That is an environment limit, not a site defect.

**Background remover service:** `GET /wp-json/seller-tools/v1/background` → `{"ready":false,"allowed":false}`, "available after the site owner connects the service". No remove.bg key is configured. The UI reports this honestly.

> Smoke test only. Functional testing with valid, invalid and large files is Phase 01 (TOOL-AUDIT.md).

## 5. Existing tool inventory (from source code, plugin 1.2.1)

Tool catalog is a PHP array `seller_tools_catalog()` with 9 tools in 4 categories (pricing, images, documents, marketing).

| Tool | Processing | Stated limits / notes from code |
|---|---|---|
| Marketplace resizer | Browser (canvas) | Presets Amazon 2000², Daraz 1200², Shopify 2048², Etsy 2000², custom up to 4096 px; contain/cover; listing/gallery/banner selector; labelled "editable canvas presets" |
| Compressor | Browser | JPG/WebP/PNG, quality 1–100 |
| Square maker | Browser | Size up to 4096, padding 0–40 %, white/transparent/custom colour; states it does not remove backgrounds |
| Bulk & ZIP | Browser | **50 images, 20 MB each, 100 MB per batch**; ZIP download; cancel ("stop after current image") |
| Watermark | Browser | Logo (≤5 MB) or text; 5 positions (not 9); opacity, size, edge spacing, rotation; no tiling |
| Background remover | **Server → remove.bg** | Admin-only unless "public" enabled; daily site cap (default 30, max 500); 3 req/10 min per IP for visitors; JPG/PNG/WebP ≤10 MB and ≤25 MP; atomic lock for quota; key stored in WP option or `SELLER_REMOVE_BG_KEY` constant |
| Profit calculator | Browser | Price, cost, shipping, fees, ads, other; currency list USD/CAD/GBP/EUR/AUD/PKR/INR/AED |
| Invoice generator | Browser | Business/client, number, dates, line items, single tax %, notes, print/save as PDF via browser |
| UTM builder | Browser | Standard 5 UTM params |

Brand assets in plugin: `assets/amazon.png`, `daraz.svg`, `shopify.svg`, `etsy.svg`. Their official-source status still needs verification in Phase 02.

## 6. Indexing, SSL, cron, email

| Item | Finding | Risk |
|---|---|---|
| SSL | HTTPS works (CDN). WordPress `siteurl` registered as `http://…` in Hostinger's install record | Low; verify `home`/`siteurl` after the WP connection |
| Search-engine visibility | **Fixed (R1):** `blog_public` 1 → 0 ("Discourage search engines") | ✅ |
| Meta robots | **Fixed (R1):** all 12 routes now output `noindex, nofollow` | ✅ |
| `X-Robots-Tag` header | **Added (R1):** `noindex, nofollow` on all pages and REST responses, via `wp-content/novamira-sandbox/seller-staging-noindex.php` | ✅ Static files (images/CSS) are not covered, because `.htaccess` is write-protected by Novamira |
| Sitemap | **Fixed (R1):** `/wp-sitemap.xml` → 404 | ✅ |
| robots.txt | Unchanged: `Googlebot: Disallow /`, others `Allow /` (served by Hostinger's preview-domain mu-plugin) | Acceptable: crawlers must be able to fetch a page to see its `noindex`. |
| Access protection | Not enabled. **noindex is not access control.** Anyone with the URL can open the dev site. | Add a password before any private or test customer data exists (Phase 06) |
| REST user list | `/wp-json/wp/v2/users` lists the `admin` username publicly | Medium; harden later (Phase 12, or earlier with approval) |
| Cron | Default WP-Cron (runs on page visits). No real server cron verified. | Needed before reminders and renewals (Phase 09/07) |
| Email | No SMTP plugin, so PHP `mail()` default. No sender or SPF/DKIM/DMARC configured for this domain. | Fine for dev isolation; must be set up in Phase 09 |
| Caching | No caching plugin on dev; CDN marks HTML as DYNAMIC | Fine |

## 7. Backup record

### Backup R0: created and verified 2026-11-11 (before any change)

**Location:** `/home/u56136xxxx/seller-toolkit-backups/2026-11-11-R0/` on the hosting account. It sits **outside `public_html`**, so it is not web-accessible. Folder permissions are `0700`. It can be reached through hPanel File Manager.

| File | Content | Size | SHA-256 (first 16) |
|---|---|---|---|
| `database.sql` | Full DB: 40 tables, 504 rows (PHP-generated; `mysqldump` is disabled on this host) | 823 KB | `93fa0bdf8a7ca2cf` |
| `files-1-custom-config-root.zip` | Root files incl. `wp-config.php`, `.htaccess`, `.private`, mu-plugins, sandbox, uploads, **seller-tools plugin**, **seller-toolkit theme** | 0.5 MB, 61 files | `73a7127199f9627b` |
| `files-2-themes.zip` | Other themes | 23.6 MB, 4,037 files | `630722c40a61beae` |
| `files-3-plugins.zip` | Other plugins | 63.5 MB, 11,224 files | `282ebad7b292c959` |
| `files-4-wp-core.zip` | `wp-admin`, `wp-includes` | 22.5 MB, 3,322 files | `d6f2126f741ae932` |
| `MANIFEST.txt`, `RESTORE.txt` | Full checksums and step-by-step restore instructions | | |

**Verification performed:**
- ✅ Every zip opened with an integrity check (`ZipArchive::CHECKCONS`): OK ×4
- ✅ Completeness: live `public_html` has **18,644 files**, the zips hold **18,644 unique entries**, **0 missing**
- ✅ Readability: extracted the custom plugin and theme (23 files) to a temp folder; **all 23 SHA-256 hashes match the live files**. Temp folder removed afterwards.
- ✅ Database: 40 `CREATE TABLE` statements = 40 live tables; 504 `INSERT` rows = 504 live rows; end-of-dump marker present; all 12 page IDs (9–29) and 16 `[seller_tool…` shortcodes present
- ⚠️ **Same-server copy only.** It does not protect against hosting-account or server loss. **Recommended:** download it once, or generate an hPanel backup, so a copy exists off the server.
- ⏳ A full restore rehearsal on a separate safe copy is scheduled for Phase 12. It must never be run over a live site.
- 🔒 `database.sql` and `files-1` contain the admin password hash, `wp-config.php` and Novamira OAuth tokens. Never upload them anywhere public.

**Older source history found on the server:** `wp-content/novamira-sandbox/` holds earlier packages: `seller-toolkit-tools.zip`, `seller-tools-before-studio.zip`, `seller-studio-v110.zip`, `seller-tools-before-v120.zip`, `seller-studio-v120.zip`, `seller-studio-v122.zip`, and `seller-toolkit-theme.zip`. They are web-blocked (HTTP 403) and included in backup R0.

## 8. Rollback points

| Point | When | What changed | How to roll back |
|---|---|---|---|
| **R0** | 2026-11-11, before any change | Nothing (baseline) | `RESTORE.txt` in the backup folder: extract zips 4 → 3 → 2 → 1 into `public_html`, then import `database.sql` with phpMyAdmin. **Dev website only.** Never restore over royalvisiontraders.com or royalvisionlaw.com. |
| **R1** | 2026-11-11, staging protection | (a) option `blog_public` 1 → 0; (b) new file `wp-content/novamira-sandbox/seller-staging-noindex.php` | (a) Settings → Reading → untick "Discourage search engines"; (b) delete or disable that sandbox file. No other files or content were touched. |

**Post-change regression (R1):** 12/12 routes return 200 with no site JS errors and forms initialised. Real functional test: the Image Resizer turned an 800×500 PNG into a **2000×2000 JPEG** inside `product-images.zip`. The ZIP downloaded and opened correctly. The background-remover endpoint is still `ready:false`.

## 9. Dependency & license checklist

| Dependency | Purpose | Status | Cost / action |
|---|---|---|---|
| Hostinger Business hosting | Hosting for dev (and live sites) | Active | Existing |
| WordPress 7.1.4 | Core | OK | Free |
| Elementor (core) | Page builder | Installed 4.4.0 | Free |
| **Elementor Pro (official)** | Theme Builder header/footer, global templates, forms (spec requirement) | ❌ **Not licensed. "PRO Elements" is installed instead.** | **Owner decision:** buy an official Elementor Pro licence, or explicitly approve PRO Elements and accept no vendor support/updates. Not purchased by us. |
| WooCommerce | Commerce source of truth (Phase 07) | Not installed | Free |
| WooCommerce Subscriptions (official) | Recurring billing (Phase 07) | Not installed / not licensed | Paid licence, needs approval |
| Payment gateway (e.g. Safepay or another provider) | Checkout | Not chosen | Depends on business country and merchant eligibility (Phase 07) |
| remove.bg API | Background removal | Code exists, **no key** | Pay-per-use credits (Phase 10) |
| SMTP / transactional email provider | Reminders | Not configured | Phase 09 |
| Novamira | AI agent bridge | Installed 1.12.7 | Confirm licence/terms; keep for dev only, review before launch |

## 10. Isolation from live billing and messaging

- ✅ Dev has no WooCommerce, no gateway, no SMTP, no customer data. Nothing on it can charge or message real customers today.
- ✅ This project's work is limited to the dev host. Connectors that point at live sites are excluded by rule.
- ⚠️ Same hosting account as the live store: hPanel actions (backups, PHP version, deletes) must always be checked against the domain name before running.
