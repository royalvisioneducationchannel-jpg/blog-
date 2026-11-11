# Phase 03 — WordPress & Elementor Foundation (dev site)

_Completed 2026-11-11 on `lightyellow-alligator-410734.hostingersite.com` only. Backup R3 was taken first._

## 1. Theme decision

The existing `seller-toolkit` theme is **kept and adapted, not replaced**. It is a small classic theme, and the tools don't depend on it. The changes:

- **1.1.0:** registers Elementor/PRO Elements `header` and `footer` locations. The theme's own header and footer stay as a **fallback** when no Elementor template applies. Enqueues `assets/tokens.css` with self-hosted Poppins 500/600/700 (OFL) and the approved tokens.
- **1.1.1:** adds the logo mark and 29 single-file SVG icons (`assets/icons/*.svg`) for use in Elementor image widgets.
- **1.1.2:** maps the legacy theme palette (indigo #4F46E5, Inter) to the approved tokens (#2457D6, Poppins headings, Arial body).

## 2. Elementor / PRO Elements configuration

| Setting | Value |
|---|---|
| Global colours (kit #32) | Primary #2457D6, Secondary #626B78, Text #20252B, Accent/Success #157347, plus 8 custom colours: surface, tint, border, input border, warning, error, white, primary hover |
| Global fonts | Headings Poppins 600, Subheadings Poppins 500, Body Arial 16, Labels Poppins 600 |
| Content width | 1240 px |
| Breakpoints | Mobile ≤ 767, Tablet ≤ 1024 |
| Google Fonts | **Disabled** (`elementor_google_font = 0`). Poppins is served from the theme; measured 0 Google Fonts requests. |
| Previous kit settings | Saved in option `seller_tools_kit_backup_r3` (and in backup R3) |

**Templates (Flexbox Containers, global colour references, no HTML widgets for layout):**

| ID | Template | Type | Condition |
|---|---|---|---|
| 39 | ST · Site header | header | Entire site |
| 41 | ST · Site footer | footer | Entire site |
| 43 | ST · Tool card | container | Reusable |
| 45 | ST · Pricing card | container | Reusable (shows "Price pending" / "Not on sale yet") |
| 47 | ST · FAQ | container | Reusable (accordion widget) |
| 49 | ST · Comparison table | container | Reusable |
| 51 | ST · Trust note | container | Reusable |
| 53 | ST · CTA band | container | Reusable |

Primary menu `st-primary` links only to **existing** pages: All tools, Product image studio, Profit calculator, Invoice generator. Pricing and Guides are added in Phase 04, so there are no dead links. The footer states that business details and policies will be published before launch. The privacy page is still a draft and is not linked.

## 3. Plugin foundation: seller-tools 1.3.0

New modules (`includes/`, `admin/`):

| Module | What it does |
|---|---|
| `ST_Registry` | **Single source** for the directory, pricing and portal visibility. Fields: id, name, category, route, access (free/credits), processing (browser/server), limits, credit_cost, provider, enabled, available, availability_reason, support_url, description, keywords. `seller_tools_catalog()` (used by the shortcode and theme directory) is now derived from it. |
| `ST_Plans` | One catalog: Free, Image Pro, Seller Desk, Seller Suite (bundle) and credits. Prices are `null` (pending). **Nothing is sellable** until status = available **and** a price exists. Bundle limits are the union (max), never doubled. |
| `ST_Entitlements` | Server-side derivation per workspace. Today every workspace = Free; grant sources plug in through a filter (WooCommerce in Phase 07). |
| `ST_Workspaces` | Workspace and member tables; access and role checks, so every private query can be scoped |
| `ST_Ledger` | Usage ledger with a **unique idempotency key**; reserve → finalize/release as single conditional UPDATEs |
| `ST_Audit` | **Append-only** events (no update/delete API). Keys matching key/secret/token/password/card/email/phone/address are redacted. |
| `ST_DB` | Versioned migrations (`seller_tools_db_version` = 1). Runs on activation and when the stored version is older (package upgrades). |
| REST | `GET /wp-json/seller-tools/v1/registry` and `/plans` (public, read-only, cacheable, PKR, prices null) |
| Admin | **Seller Toolkit** menu (manage_options): Tool registry, Plans, System (schema/tables), Audit log. Read-only. |
| `uninstall.php` | **Keeps all data** unless `define('SELLER_TOOLS_DELETE_DATA', true)` is set in wp-config.php |

Tables: `wp_st_workspaces`, `wp_st_workspace_members`, `wp_st_usage_ledger`, `wp_st_audit_events`.

Modules **not built yet** (planned for the phase that needs them, to avoid empty code): private projects (Phase 05/06), subscriptions integration (Phase 07), communications (Phase 09), editable admin controls with audit (Phase 09/10).

## 4. Verification evidence

**Server tests (23/23 PASS), run on the dev site.** Workspace and ledger data was written inside a DB transaction and **rolled back**:
schema v1; 4 tables present; unique idempotency index; migrate re-run is idempotent; catalog = 9 tools from the registry; background remover unavailable with a reason; shortcodes still render studio and invoice; no plan sellable; workspace isolation (user 1 can't access workspace 2); owner role; duplicate ledger key → 1 row; reserve counts; finalize once only; release returns the credit; ledger scoped per workspace; overlapping grants (Image Pro + Suite) not doubled; another workspace stays Free; audit redacts secrets; rollback left no test rows.

**Local tests:** PHP 8.3 lint for all 11 new files, plus a stubbed-WordPress smoke test of the registry, plans, entitlements and redaction.

**Browser (Chromium):**
- Elementor header and footer render on all routes; theme fallback header not used; **0 Google Fonts requests**; h1 = Poppins.
- No horizontal overflow at 1440 / 390 / 360 (5 routes each). Header items are fully inside the viewport at 1440 / 1024 / 768 / 390 / 360 (checked separately, because `overflow-x: clip` would otherwise hide problems). The desktop menu is on one row.
- The mobile burger menu opens full width (0–390 px) with 48 px tap targets.
- **Existing tools still work** (full Phase 01 matrix re-run): image studio 21 tests identical to baseline (same known H2 limit); invoice total 409.49; profit PKR 750.00; UTM validation; batch limits; 12/12 pages without JS errors; real resize → ZIP download.

## 5. Issues found and fixed during this phase

1. PRO Elements `save_conditions` needs condition arrays, not strings. Fixed in the builder.
2. The mobile header overflowed by 26–28 px (nav dropdown and CTA). Fixed with header `overflow-x: clip`, 16 px mobile padding, the CTA hidden on mobile (the same link is in the menu), and the dropdown anchored full width under the header.
3. The desktop menu wrapped to two rows because nested containers default to 100% width. Fixed with `width: auto` on the brand group.
4. Inner containers inherited Elementor's default 10 px padding. Set to 0 in all ST templates.

## 6. Releases (dev server, `~/seller-toolkit-backups/releases/`, outside webroot)

| Package | sha256 (first 12) |
|---|---|
| seller-tools-1.2.2.zip | ecf1ab06094c |
| seller-tools-1.3.0.zip | 8c22ce2f43dc |
| seller-toolkit-theme-1.1.0.zip | 91e18694013d |
| seller-toolkit-theme-1.1.1.zip | 8efea51bac06 |
| seller-toolkit-theme-1.1.2.zip | f50b1e20a77c |

## 7. Rollback (R3 → pre-Phase-03 state)

1. Plugin: install `releases/seller-tools-1.2.2.zip` with the plugin upgrader (overwrite). New tables stay; they are harmless and unused by 1.2.2.
2. Theme: restore `themes/seller-toolkit` from `2026-11-11-R3-pre-phase03/seller-code.zip`, or upload it as theme 1.0.0.
3. Elementor: move templates #39–#53 to trash (the header/footer conditions go with them); restore kit #32 settings from option `seller_tools_kit_backup_r3`; set `elementor_google_font` back to its previous value (unset = enabled).
4. Menu: delete the `st-primary` menu.
5. Full fallback: `R3/database.sql` (503 rows) plus `seller-code.zip`.
