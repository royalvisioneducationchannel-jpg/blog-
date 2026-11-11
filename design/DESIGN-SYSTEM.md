# Seller Toolkit — Design System v0.1 (Phase 02, for review)

_Status: **awaiting owner design review**. Not yet implemented on the site. Phase 03 maps these tokens into Elementor (PRO Elements) global colours and fonts._

## 1. Colour tokens (contrast measured)

| Token | Hex | Use | Contrast on white / #F6F7F9 / #EDF2FF |
|---|---|---|---|
| `--bg` | #FFFFFF | Main background | — |
| `--surface` | #F6F7F9 | Alternate sections, app background | — |
| `--text` | #20252B | Primary text | 15.4 / 14.4 / 13.8 ✅ |
| `--text-2` | #626B78 | Secondary text | 5.39 / 5.03 / 4.81 ✅ AA |
| `--border` | #DFE3E9 | Decorative dividers and card borders only | 1.29 (decorative, not for controls) |
| `--input` | #848D9A | **Form control borders** (added: non-text contrast ≥ 3:1) | 3.36 / 3.13 ✅ |
| `--primary` | #2457D6 | Actions, links, selected | 6.16 / 5.75 / 5.50 ✅. White on primary 6.16 ✅ |
| `--tint` | #EDF2FF | Selected and tinted backgrounds | — |
| `--success` | #157347 | Free/Done/Active | 5.87 / 5.48 / 5.24 ✅ |
| `--warning` | #946200 | In development / expiring | 5.24 / 4.89 / 4.68 ✅ |
| `--error` | #B42318 | Failed / needs action | 6.57 / 6.13 / 5.87 ✅ |

All text tokens pass WCAG AA (4.5:1) on all three backgrounds. Status is never shown by colour alone: every badge has a word.

## 2. Typography

- **Headings and prominent labels:** Poppins 500/600/700, **self-hosted** (`design/fonts/`, OFL-1.1, Latin subset, about 8 KB each, `font-display: swap`). No Google Fonts request.
- **Body, forms, tables:** Arial / system sans-serif, 16 px base, line-height 1.55. Forms use 16 px to prevent iOS zoom.
- Scale: H1 `clamp(32px, 4.4vw, 52px)` · H2 `clamp(24px, 2.6vw, 32px)` · H3 18 · body 16 · small 13–14.
- Urdu: Arial/system fallback is used for now. An Urdu (Nastaliq/Naskh) font choice is a **pending decision** before translated pages exist.

## 3. Layout & spacing

- 4/8 px rhythm: 4, 8, 12, 16, 24, 32, 48, 64.
- Radii: 8 px (controls), 10 px (notes), 12 px (cards), 16 px (hero preview only).
- Content max width **1240 px**; side gutter 24 px (16 px under 600 px). Tested at **390 and 1440 px with 0 horizontal overflow** on all 6 screens.
- Touch targets ≥ 44 px for buttons and inputs.
- Motion: none needed. `prefers-reduced-motion` disables transitions.

## 4. Brand mark

`design/brand/logo-mark.svg`: an original geometric mark. It is a rounded blue square with a white price tag (a seller cue), a punched hole and a single cut line (a "tool" cue). It is used at 32 px in the header and as a favicon candidate. The wordmark "Seller Toolkit" is set in Poppins 600.
Rules: keep clear space of ¼ of the mark size; don't recolour the tag; on dark backgrounds use the same mark (it carries its own blue field).

## 5. Icon family

`design/brand/icons.svg`: 32 original icons on a 24 px grid with a 1.75 stroke, round caps and joins, using `currentColor`. They cover the tools (resize, compress, square, bulk, watermark, cutout, profit, invoice, link), Seller Desk (box, truck, wallet), portal navigation (home, folder, palette, chart, card, bell, help, sliders, user) and states (check, lock, clock, alert, device, upload, down). Tool icons are **not** third-party logos.

**Marketplace logos** (Daraz, Shopify, Amazon, Etsy) are reused from the plugin's `assets/` folder. ⚠️ Their **official-source status still has to be verified** before public launch. Every use carries "not affiliated" wording and links to a real working destination (the preset).

## 6. Components (in `design/prototypes/styles.css`)

Buttons (primary, secondary, ghost, disabled) · badges (Free, Paid, In development/Planned, Not available yet, Error, Demo) · tool card · plan card · comparison table · note/trust note (ok, warn) · form field · segmented control · Studio Guide · app shell (sidebar, mobile tabs) · KPI card · usage bar · timeline · footer.

## 7. Truthful trust components (rules applied in the prototypes)

- Processing location stated where it matters ("Runs on your device", "Photos stay on this device").
- Limits shown **before** use (50 / 20 MB / 100 MB / 16 MP output).
- Unfinished products are labelled **In development / Planned**, show **Price pending** and a disabled **"Not on sale yet"** button.
- The background remover is shown as **Not available yet (provider change)**.
- Renewal type is shown explicitly ("Renew manually", because no gateway is chosen yet).
- **No** fake customer counts, star ratings, "most popular", savings %, certifications, AdSense or payment badges.
- Demo data is always tagged **Demo**. Business identity fields are placeholders for the owner to supply.

## 8. Studio Guide (assistant)

A small dark tile with the robot icon (32 px) plus a heading "Studio Guide · N suggestions". It sits **inline** in the page flow (never floating over content) and gives deterministic, rule-based suggestions that link to the affected item. It is labelled "rule-based tips, not an AI model".

## 9. Screens delivered for review

| Screen | File | Previews (`design/previews/`) |
|---|---|---|
| Homepage | `prototypes/home.html` | home-1440.png, home-390.png |
| Pricing | `prototypes/pricing.html` | pricing-1440.png, pricing-390.png |
| Tool workspace (resizer) | `prototypes/tool.html` | tool-1440.png, tool-390.png |
| Customer dashboard (/app/) | `prototypes/dashboard.html` | dashboard-1440.png, dashboard-390.png |
| Billing | `prototypes/billing.html` | billing-1440.png, billing-390.png |
| Admin customer detail (CRM) | `prototypes/admin-customer.html` | admin-customer-1440.png, admin-customer-390.png |

**Known gaps (intentional for review):** product photos are placeholders (real or licensed photography to be supplied); footer identity and policies are placeholders; PKR prices are pending.

## 10. Elementor mapping (Phase 03 plan)

Global colours = tokens in §1. Global fonts: Primary = Poppins 600, Secondary = Arial, Text = Arial 16, Accent = Poppins 500. Container width 1240. Breakpoints: mobile ≤ 767, tablet ≤ 1024. Reusable templates: tool card, plan card, FAQ, comparison table, trust note, CTA band, header, footer. Application screens (studio, portal, admin) are rendered by the plugin, not by Elementor HTML widgets.
