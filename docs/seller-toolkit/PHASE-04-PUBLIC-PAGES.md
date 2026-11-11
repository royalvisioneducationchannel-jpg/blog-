# Phase 04 — Homepage, Directory & Sales Pages (dev site)

_Completed 2026-11-11. Backup **R4** was taken first (`~/seller-toolkit-backups/2026-11-11-R4-pre-phase04/`, DB 869 rows plus plugin and theme code)._

## 1. Pages (all Elementor, Flexbox Containers, template "Elementor Full Width" with the site header and footer)

| Page | URL | Notes |
|---|---|---|
| Home | `/` | Hero ("Tools and a workspace for your selling day."), **real studio screenshot with demo photos** (labelled "DEMO DATA" and captioned), marketplace presets (not-affiliated note), live tool directory, plans, 3-step workflow, FAQ, CTA |
| Tools | `/tools/` | Registry-driven directory with **search, task filter (Photos/Money/Marketing) and price filter (Free / Paid-credits)** |
| Pricing | `/pricing/` | PKR; Monthly/Yearly toggle; **pending-price state**; every paid plan shows **"Not on sale yet" (disabled)**; comparison table; credits listed separately as not available; renewal/cancellation/tax notes |
| Image Pro / Seller Desk / Seller Suite | `/pricing/image-pro/` etc. | Plan details with status (In development / Planned), included features and an explicit "Not included" list |
| How it works | `/how-it-works/` | Processing location, limits, why background removal is unavailable |
| Guides | `/guides/` | Task links to tool how-tos, plus a clearly labelled list of guides being written (no thin placeholder articles) |
| Help | `/help/` | 8-question FAQ, including the compressor's 16 MP limit and honest browser-testing status |
| Contact | `/contact/` | **"Coming soon"**, as requested. No form, so no personal data collected. Links to Help. |
| 10 tool pages | `/tools/*` | Converted to Elementor. Original shortcodes and the existing how-to text are kept; a processing/privacy note and help links were added. |

**Menu (`st-primary`):** Tools ▾ (All tools plus 9 tool pages), Pricing, How it works, Guides, Help. **Footer:** All tools, Pricing, How it works, Guides, Help, Contact.
**No Sign in link** yet: the customer portal arrives in Phase 06, and a link now would be a dead CTA.

## 2. Plugin seller-tools 1.4.0 (single sources, no duplicated prices)

- `[seller_tools_directory]`: cards rendered from `ST_Registry`, with icon, Free / Credits / Not-available badge, description or availability reason, and processing location. Search plus filters work without page reload; **without JS all tools stay visible**.
- `[seller_tools_pricing plan="" table="1"]`: rendered from `ST_Plans`. Null prices show "Price pending"; anything not sellable shows a disabled button; the interval toggle switches every price; bundles are labelled as not doubling limits.
- `assets/public.css` / `public.js` load only on pages that use these shortcodes.

## 3. Verification

| Check | Result |
|---|---|
| 20 public routes | All **200**, exactly **one H1** each, no `href="#"` links, no site JS errors, tool forms initialised |
| Internal links (header, menu, content, footer) | **20 unique, 0 broken** |
| Directory | All 9 · Money 2 (profit, invoice) · Paid 1 · search "invoice" 1 · empty state shows · reset restores 9 · live status "9 of 9 tools shown" |
| Pricing toggle | Yearly shows 4 yearly prices (Free "PKR 0 / year", others "Price pending"), monthly hidden; 3 disabled purchase buttons |
| Overflow | **None** on all 20 routes at 360 / 390 / 768 / 1024 |
| Header | Items inside the viewport at 1440 / 1024 / 768 / 390 / 360; desktop menu on one row |
| Existing tools | Full Phase 01 matrix re-run: **identical to baseline** (resize/compress/square/bulk/watermark/limits/cancel; invoice 409.49; profit PKR 750.00; UTM validation) |

Fixed during the phase: the marketplace cards stretched full width (nested-container default width) → `width: auto`.

## 4. Known limitations (honest)

- The hero interface preview uses **synthetic demo photos**. Real product photography is still needed (owner to supply, or approve licensed stock).
- Marketplace logos come from the plugin's existing assets. Their official-source status is still to be verified before launch.
- SEO titles, descriptions and structured data are Phase 11. The site remains noindex.
- Policies and business identity are Phase 11. The footer says they will be published before launch.

## 5. Rollback (R4)

Restore `R4/database.sql` (pages, menu, Elementor data) and `seller-code.zip`, or install `releases/seller-tools-1.3.0.zip` to remove the shortcodes. New pages can be trashed individually: IDs 72, 75, 78, 81, 84, 87, 90, 93. Media attachment #67 is the hero screenshot.
