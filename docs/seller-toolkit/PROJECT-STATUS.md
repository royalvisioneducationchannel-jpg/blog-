# PROJECT STATUS — Seller Toolkit

_Last updated: 2026-11-11_

## Current state

| Phase | Name | Status |
|---|---|---|
| — | Build pack saved to repository | ✅ Done |
| 00 | Connect, inspect and protect | ✅ Complete (follow-up: off-server backup copy) |
| 01 | Audit tools, validate needs, define scope | 🟡 **Deliverables done. Waiting on owner decisions** (§ Needs owner input) |
| 02 | Design system and prototypes | ⏭️ Next, after the decisions |
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
- **H1:** the invoice shows PKR totals that don't add up (350 + 60 shown as 409). This is a display rounding bug.
- **H2:** the compressor and watermark fail for 16–36 MP photos, even though uploads up to 36 MP are accepted.
- **H3:** **remove.bg ends its standalone service and self-serve API on 1 Dec 2026** (first-party FAQ). Unused credits expire then, and the current background-remover adapter will stop working. → Do not buy remove.bg credits. Pick a replacement provider in Phase 10 (Photoroom API at $0.02/image, or Leonardo.ai).
- COD/courier reconciliation for Pakistani sellers **already exists** (Wasooli, launched 5 Nov 2026; Oshi Courier). Seller Desk needs pilot evidence before it is built or sold.
- No deletions or merges are recommended. Dead legacy code (`views/image.php`) can be cleaned up later with approval.

## Decisions

- Working brand name: **Seller Toolkit** (final name pending).
- Only the `Seller_Toolkit_Dev` connector is used; live-site connectors are excluded.
- Free limits stay unchanged until a deliberate plan change is approved.
- Background removal is not sold until a tested replacement provider adapter exists.
- Seller Desk is not sold until pilot evidence, the demo flow and isolation tests pass.

## Needs owner input

**Blocking Phase 02 (brand/design) and the pricing work:**
1. Final **brand name**
2. **Business country** and legal entity
3. **Currency** (PKR only, or PKR + USD)
4. **Licence budget:** official Elementor Pro or PRO Elements; WooCommerce Subscriptions
5. **Refund/cancellation** rules

**Optional approvals:**
6. Fix **H1 (invoice totals)** now instead of waiting for Phase 05 (small, low-risk change on dev)
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

Phase 01 made **no site changes** (read-only testing).

## Exact next step

1. Owner answers decisions 1–5 (and optionally 6–8).
2. Record them here and in `PRODUCT-SCOPE.md` §6.
3. Start **Phase 02**: design tokens, original brand mark and icon family, and desktop + mobile previews of the homepage, pricing, one tool workspace, customer dashboard, billing screen and admin customer detail. One concrete design review before broad implementation.
