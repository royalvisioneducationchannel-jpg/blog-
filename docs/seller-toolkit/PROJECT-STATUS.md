# PROJECT STATUS — Seller Toolkit

_Last updated: 2026-11-11_

## Current state

| Phase | Name | Status |
|---|---|---|
| — | Build pack saved to repository | ✅ Done |
| 00 | Connect, inspect and protect | ✅ Complete (follow-up: off-server backup copy) |
| 01 | Audit tools, validate needs, define scope | ✅ Complete (owner decisions recorded) |
| 02 | Design system and prototypes | ✅ Approved by owner (2026-11-11) |
| 03 | WordPress and Elementor foundation | ✅ Complete — see `PHASE-03-FOUNDATION.md` |
| 04 | Homepage, directory and sales pages | ✅ Complete — see `PHASE-04-PUBLIC-PAGES.md` |
| 05 | Polish tools and advanced workflows | ⏭️ **Next** |
| 06 | Customer portal, workspaces, access control | Not started |
| 07 | Checkout, payments, subscription lifecycle | Not started |
| 08 | Seller Desk operations workspace | Not started |
| 09 | Platform CRM and renewal communications | Not started |
| 10 | Paid providers, usage limits, spending controls | Not started |
| 11 | Content, trust, SEO, AdSense readiness | Not started |
| 12 | Security, reliability, full journey QA | Not started |
| 13 | Final domain, activation, handover | Not started |

## Phase 01: completed work

| Deliverable | File |
|---|---|
| Tool audit: 40+ tests, findings by severity, keep/improve recommendations | `TOOL-AUDIT.md` |
| Competitor and provider research (first-party, dated) | `COMPETITOR-RESEARCH.md` |
| 5 interview questions and pilot checklist (nobody contacted) | `VALIDATION-PLAN.md` |
| Catalog, plan-feature-limit matrix, release plan, cost worksheet, DRAFT prices | `PRODUCT-SCOPE.md` |
| Re-runnable audit tests | `tests/tool-audit/` |

**Key results**
- 8 of 9 tools work and process locally in the browser. Free limits are confirmed at **50 files / 20 MB each / 100 MB batch**.
- **H1:** the invoice showed PKR totals that didn't add up. ✅ **Fixed in seller-tools 1.2.2** (owner-approved, 2026-11-11). See the change log below.
- **H2:** the compressor and watermark fail for 16–36 MP photos, even though uploads up to 36 MP are accepted.
- **H3:** **remove.bg ends its standalone service and self-serve API on 1 Dec 2026** (first-party FAQ). Unused credits expire then, and the current background-remover adapter will stop working. → Do not buy remove.bg credits. Pick a replacement provider in Phase 10 (Photoroom API at $0.02/image, or Leonardo.ai).
- COD/courier reconciliation for Pakistani sellers **already exists** (Wasooli, launched 5 Nov 2026; Oshi Courier). Seller Desk needs pilot evidence before it is built or sold.
- No deletions or merges are recommended. Dead legacy code (`views/image.php`) can be cleaned up later with approval.

## Phase 02: delivered for review

- `design/DESIGN-SYSTEM.md`: tokens with measured contrast, typography, spacing, brand mark, icon family, components, trust rules, Studio Guide, Elementor mapping
- `design/brand/logo-mark.svg` (original mark) and `design/brand/icons.svg` (32 original icons)
- `design/fonts/`: Poppins 500/600/700, self-hosted (OFL-1.1, from npm @fontsource/poppins 5.3.0)
- `design/prototypes/`: home, pricing, tool workspace, customer dashboard, billing, admin customer detail (static HTML, Demo data)
- `design/previews/`: 12 screenshots (1440 px and 390 px), **0 horizontal overflow**, Poppins loaded

## Decisions

- Brand name: **Seller Toolkit** (final). Country: **Pakistan**. Currency: **PKR**. Page builder: **PRO Elements** (owner accepted; no Elementor vendor support).
- Add `--input` #848D9A for form-control borders, because #DFE3E9 only reaches 1.29:1.
- Only the `Seller_Toolkit_Dev` connector is used; live-site connectors are excluded.
- Free limits stay unchanged until a deliberate plan change is approved.
- Background removal is not sold until a tested replacement provider adapter exists.
- Seller Desk is not sold until pilot evidence, the demo flow and isolation tests pass.

## Needs owner input

**Blocking Phase 03:**
1. **Design review** of the Phase 02 previews: approve, or list changes

**Needed later (Phase 07 / 11):**
2. WooCommerce Subscriptions licence budget
3. Refund/cancellation rules (and the grace period after a missed renewal)
4. Legal entity name, city and contact details for the footer and policies
5. Real product photos (or approval for licensed stock) for the hero and tool previews
6. Urdu font preference (before translated pages)

**Optional approvals:**
6. ~~Fix H1 now~~ ✅ Done (seller-tools 1.2.2)
7. Run the **seller pilot** (VALIDATION-PLAN.md)
8. Fund a capped free background-removal trial (default: no)

**Still recommended from Phase 00:** off-server backup copy; make the GitHub repo private.

## Not yet live / not configured

- Background-removal provider (remove.bg is sunsetting; replacement not chosen)
- WooCommerce, subscriptions, payment gateway, SMTP, real cron
- Dev-site password protection (noindex is not access control)
- `WP_ENVIRONMENT_TYPE` = production on dev (set it to staging before Phase 07)

## Environment notes

- The cloud environment `blog- (Seller Toolkit dev)` allows: the dev host, www.remove.bg, www.photoroom.com, www.iloveimg.com, apps.shopify.com, oshicourier.pk (research). WebFetch cannot resolve hosts here, so use `curl` through the proxy.

## Rollback points

| Point | What | Roll back |
|---|---|---|
| R0 | Baseline full backup, 2026-11-11 (`~/seller-toolkit-backups/2026-11-11-R0/`) | `RESTORE.txt` (dev site only) |
| R1 | `blog_public` 1 → 0; `wp-content/novamira-sandbox/seller-staging-noindex.php` | Re-tick visibility; delete the sandbox file |

| R2 | Before the H1 fix: exact copy of plugin 1.2.1 (`~/seller-toolkit-backups/2026-11-11-R2-pre-invoice-fix/seller-tools-1.2.1.zip`, sha256 `3253afd2…`) | Install that ZIP with WordPress's plugin upgrader (overwrite), or Plugins → Add New → Upload |

## Change log (dev site)

| Date | Version | Change | Verified |
|---|---|---|---|
| 2026-11-11 | seller-tools **1.2.2** | **H1 fix:** `tools.js` `money()` always shows 2 decimals; script version bumped 1.2.1 → 1.2.2 (cache busting; JS is browser-cached for 7 days). Deployed as a ZIP (`~/seller-toolkit-backups/releases/seller-tools-1.2.2.zip`, sha256 `ecf1ab06…`) through WordPress's plugin upgrader. Plugin stayed active. | Invoice: 99.99 + 250.00 = 349.99, tax 59.50, total 409.49 on screen and in the PDF. Regression: 12/12 pages OK, resize to ZIP OK, profit and UTM OK, background-remover status unchanged, noindex still active. |

**Deployment method for plugin changes:** build the ZIP (exact, counted replacements) → back up the current version → install with `Plugin_Upgrader` (overwrite) → verify. Novamira's rule against writing PHP outside the sandbox is respected; nothing bypasses it.

## Phase 03 summary

- Theme kept and adapted: 1.1.0 → 1.1.2 (Elementor locations with theme fallback, self-hosted Poppins and tokens, icons, legacy palette mapped to tokens).
- PRO Elements: global colours and fonts, width 1240, Google Fonts off. Header and footer templates on the entire site; 6 reusable components (tool card, pricing card, FAQ, comparison, trust note, CTA).
- seller-tools **1.3.0**: registry (single source), plans (nothing sellable), entitlements (server-side, no double limits), workspaces, idempotent usage ledger, append-only audit log, versioned migrations, read-only admin, data-safe uninstall, public `/registry` and `/plans` endpoints.
- Evidence: 23/23 server tests (transactional, rolled back); existing tools re-tested against the Phase 01 baseline with identical results; header verified at 5 widths; mobile menu verified open.
- Rollback point **R3**: `~/seller-toolkit-backups/2026-11-11-R3-pre-phase03/` (DB 503 rows plus plugin and theme code). Steps are in PHASE-03-FOUNDATION.md §7.

## Phase 04 summary

- 8 new pages (Pricing, 3 plan details, How it works, Guides, Help, Contact "coming soon"). Home, Tools and 10 tool pages converted to editable Elementor layouts; the original shortcodes and how-to text are kept.
- seller-tools **1.4.0**: `[seller_tools_directory]` (registry: search plus task and price filters) and `[seller_tools_pricing]` (plans: PKR, pending prices, disabled purchase, interval toggle).
- Menu: Tools ▾, Pricing, How it works, Guides, Help. Footer also has Contact. No Sign in until the Phase 06 portal exists.
- Evidence: 20 routes 200, one H1 each, 0 broken internal links, filters and toggle tested, no overflow at 360–1024, tools identical to baseline.
- Rollback point **R4**: `~/seller-toolkit-backups/2026-11-11-R4-pre-phase04/`.

## Exact next step

**Phase 05: polish the existing tools** (reuse the current code; re-run `tests/tool-audit` for the before/after inventory):
1. **H2:** add a "Limit longest side" option to the compressor and watermark for 16–36 MP photos (explicit, never silent).
2. Compressor: optional target size using real encoding attempts, with an honest "target not reached".
3. Watermark: 9 positions, tiling and spacing, a contrast-safe default, export matching the preview.
4. Profit: PKR default, COD/courier/packaging/payment-% fields, formula and assumptions shown, decimals allowed.
5. Invoice: logo, discount, tax lines (no "legally compliant" claims).
6. Resizer: make "Gallery image" meaningful or remove it; mobile tab strip.
7. Background remover: keep it disabled; the provider adapter is replaced in Phase 10.
