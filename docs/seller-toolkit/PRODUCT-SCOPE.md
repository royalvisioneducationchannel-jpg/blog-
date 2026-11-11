# Product Scope, Plans & Cost Worksheet (Phase 01 — DRAFT)

_Every price below is a **DRAFT for discussion**, not a researched market fact and not approved. Nothing is sold until the owner approves prices, the feature exists and passes tests, and payments work (Phase 07)._

## 1. Catalog (one entitlement catalog; single products + bundle)

| Product | Type | Contents | Sellable when |
|---|---|---|---|
| **Free tools** | Free, no signup | 6 image tools + studio, profit calculator, invoice, UTM. Local processing, current limits. | Now (after H1/H2 fixes in Phase 05) |
| **Image Pro** | Subscription | Saved presets, brand kits (logo, watermark, colours), saved invoice business details, private saved project **settings**. Originals stay local unless the user deliberately saves them. | After Phases 05–07 implement and test it |
| **Seller Desk** | Subscription | Products/SKUs, stock ledger, sales orders, expenses, returns, CSV import, courier-payment matching | **Only after** the pilot supports it **and** the Phase 08 demo flow plus isolation tests pass |
| **Seller Suite** | Bundle subscription | Image Pro + Seller Desk; identical limits are **not** doubled | Same as Seller Desk |
| **Background-removal credits** | One-time packs (metered add-on) | 1 credit = 1 successful removal; failed jobs are not charged | After a replacement provider adapter is built and tested (Phase 10). **The remove.bg self-serve API ends 1 Dec 2026.** |

Rules: no charging for a renamed free feature. Free limits stay as they are until a deliberate change is approved. No automatic overage billing.

## 2. Plan / feature / limit matrix (proposed starting limits, editable)

| Feature | Free (anonymous) | Free account | Image Pro | Seller Desk | Seller Suite |
|---|---|---|---|---|---|
| Local image tools | ✅ 50 files / 20 MB each / 100 MB batch (UI limit, not tamper-proof) | same | same (physical browser limits still apply) | same | same |
| Invoice, profit, UTM | ✅ | ✅ | ✅ | ✅ | ✅ |
| Saved presets | — | 3 (DRAFT) | **20** | — | 20 |
| Brand kits | — | — | **5** | — | 5 |
| Saved project slots (settings only) | — | — | **25** | — | 25 |
| Cloud media storage | — | — | **None at launch.** Propose 250 MB only after the hosting cost check (Phase 10). | — | same |
| Saved invoice business profile | — | 1 | 5 | 5 | 5 |
| Workspace / seats | — | 1 / 1 | 1 / 1 | **1 / 2** | 1 / 2 |
| Seller Desk records / CSV import size | — | — | — | **TBD from measured performance** (30 s PHP limit observed) | same |
| Background-removal credits | **0** (no paid trial unless the owner funds a capped one) | 0 | buy packs | buy packs | buy packs |
| Ads | Possibly on public tool pages (Phase 11) | public pages only | **none in workspace** | none | none |

## 3. Phased release plan

| Release | Phases | Contents | Gate |
|---|---|---|---|
| **R1 Public free tools** | 02–05 | Design, Elementor foundation, public pages, fixes H1/H2, improvements | Design review approved; audit matrix re-run passes |
| **R2 Accounts + Image Pro + credits** | 06, 07, 10 | Portal, entitlements, sandbox checkout, new BG provider adapter | Two-account isolation tests; sandbox payments; mocked-provider tests |
| **R3 Seller Desk** | 08, 09 | Ops workspace, CRM, reminders | **Pilot evidence** + demo flow + isolation tests |
| **R4 Launch** | 11–13 | Content, policies, QA, domain, live activation | Owner go/no-go; live payments approved |

## 4. Cost worksheet (fill the blanks; formulas included)

| Cost line | Value | Source / status |
|---|---|---|
| Hosting (Hostinger Business, shared with 2 other sites) | **owner to supply** (monthly) | hPanel invoice |
| Elementor Pro licence (if chosen) | **TBD** | Read the official price at purchase time; not fetched |
| WooCommerce Subscriptions licence | **TBD** | Official WooCommerce marketplace; not fetched |
| Payment gateway fees | **TBD**: % + fixed per transaction | Depends on gateway and business country (Phase 07) |
| Background removal provider cost | **$0.02 per image** (Photoroom API Basic; $20 = 1,000 images) | First-party, 2026-11-11. Leonardo.ai pricing for the migrated remove.bg API: **not yet read**. |
| Transactional email | **TBD** | Phase 09 |
| Support time | **owner estimate** (hours/month × rate) | |
| Taxes (sales tax / income tax) | **owner / accountant to supply** | Not assumed |
| Domain | **owner to supply** | |

**Formulas**
- Credit pack margin = `pack price − (credits × provider cost per success) − gateway fee(pack price) − failed-job allowance`
- Subscription contribution = `price − gateway fee − (storage GB × storage cost) − support minutes × rate`
- Break-even subscribers = `fixed monthly costs ÷ average contribution per subscriber`

**Example (DRAFT, USD, illustrative only):** a 50-credit pack at **$2.50** → provider cost 50 × $0.02 = **$1.00** → gross before gateway fees and overhead = **$1.50 (60%)**. Gateway fees are unknown, so the real margin is unknown.

## 5. DRAFT price points (for discussion only)

Reference points seen (first-party, 2026-11-11): iLoveIMG Premium US$5/month (annual) or $9 (monthly); Wasooli COD reconciliation $9.99/month; Photoroom API $0.02/image.

| Product | DRAFT monthly | DRAFT annual | Note |
|---|---|---|---|
| Image Pro | $3 equivalent | 10 × monthly | Must convert to the approved currency (PKR?) |
| Seller Desk | $6 equivalent | 10 × monthly | Only if the pilot supports it |
| Seller Suite | $8 equivalent | 10 × monthly | Less than the sum of the two, with no invented "savings %" claims |
| Credits | 50 for $2.50 equivalent | — | One-time; expiry policy TBD |

No "most popular", scarcity or savings badges. Unpriced products show a **pending-price** state and checkout is blocked.

## 6. Decisions required from the owner

1. Final **brand name** (working name: Seller Toolkit)
2. **Business country** and legal entity (decides gateway eligibility and taxes)
3. **Currency** (PKR only, or PKR + USD)
4. **Licence budget**: Elementor Pro (official) vs PRO Elements; WooCommerce Subscriptions
5. **Refund and cancellation rules** (e.g. cancel at period end; credit refunds?)
6. Whether to fund a **capped free background-removal trial** (default: no)
7. Approval to run the **seller pilot** (VALIDATION-PLAN.md)
