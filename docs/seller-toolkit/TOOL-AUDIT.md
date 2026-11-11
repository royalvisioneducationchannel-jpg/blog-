# TOOL-AUDIT — Seller Toolkit (Phase 01)

_Tested: 2026-11-11 on the dev site (plugin `seller-tools` 1.2.1). Chromium (Playwright) at 360 / 768 / 1024 / 1440 px. Firefox and Safari were **not** tested (not available in this environment)._
_Method: generated fixtures (valid, invalid and large files); every downloaded ZIP and image was opened and checked with Python/PIL. Results below are **observed**. Anything marked "hypothesis" is not yet proven._

## 1. Summary

| Tool | Route | Works? | Processing | Recommendation |
|---|---|---|---|---|
| Marketplace resizer | `/tools/image-resizer/` | ✅ | Browser | **Keep + improve** |
| Compressor | `/tools/image-compressor/` | ⚠️ Fails above 16 MP | Browser | **Keep + fix (H2)** |
| Square maker | `/tools/square-maker/` | ✅ | Browser | **Keep + improve** |
| Bulk resize & ZIP | `/tools/bulk-resizer/` | ✅ | Browser | **Keep + improve** |
| Watermark | `/tools/watermark/` | ✅ (≤16 MP) | Browser | **Keep + improve** |
| Background remover | `/tools/background-remover/` | ⛔ Not connected (honestly shown) | Server → remove.bg | **Keep page; replace provider (H3); not for sale yet** |
| Combined studio | `/tools/product-image-tools/` | ✅ | Browser | **Keep** as the shared workspace |
| Profit calculator | `/tools/profit-calculator/` | ✅ | Browser | **Keep + improve** |
| Invoice generator | `/tools/invoice-generator/` | ⚠️ Totals display bug | Browser | **Keep + fix (H1)** |
| UTM builder | `/tools/utm-builder/` | ✅ | Browser | **Keep** as a secondary marketing tool |

**Nothing should be deleted or merged.** The six image tools stay as distinct task pages, all running on one shared studio engine. The only redundancy is in code (§5, L1), not in pages.

## 2. Confirmed free limits (measured, not assumed)

| Limit | Value | How it was confirmed |
|---|---|---|
| Files per batch | **50** | 51 files added, 50 accepted, message "Batch limit reached: 50 images or 100 MB." |
| Size per file | **20 MB** | 23 MB PNG rejected with a clear message |
| Combined batch | **100 MB** | 7 × 16.5 MB → 6 accepted, 7th rejected |
| Input pixels | **36 MP and 12,000 px per side** | 42 MP JPEG rejected per file |
| Output pixels | **16 MP and 8,192 px per side** | ⚠️ Not shown before upload. This is what blocks compressor and watermark for 16–36 MP photos. |
| Width/height field | **4,096 px max** | 5,000 entered: browser validation blocks submit |
| Result batch total | 150 MB | From code; not exercised |
| Formats | JPG, PNG, WebP in; JPG, PNG, WebP out | GIF, TXT, empty and corrupt files rejected |

These match the earlier "50 files / 20 MB / 100 MB" assumption. **Recommendation: keep them unchanged** until a deliberate plan change is approved.

## 3. Test matrix and results

### Image studio (all local; no upload to the server was observed)

| ID | Test | Result |
|---|---|---|
| R1 | 3000×2000 JPG → Amazon preset, contain | ✅ 2000×2000 JPEG, 1.83 MB → 341 KB, ZIP valid |
| R2 | Daraz preset, cover/crop | ✅ 1200×1200, crop centred |
| R3 | Transparent PNG with transparent background | ✅ Format auto-switched JPG → PNG; alpha preserved (0–255) |
| R4 | GIF, TXT, empty, 23 MB, corrupt JPEG | ✅ First four rejected at add time; corrupt file → "Unable to decode this photo" per file |
| R5 | 42 MP input | ✅ Rejected with a clear message |
| R6 | Custom 5000×5000 | ⚠️ Blocked only by the native browser bubble ("≤ 4096"); no in-page message |
| C1 | Compress 3000×2000 JPG at q82 | ✅ 1.83 MB → 858 KB, "54.2% smaller", dimensions kept |
| C2 | Compress 5000×4000 (20 MP, 12 MB) | ❌ **Fails**: "Output is limited to 16 megapixels… Use the resizer first." |
| C3 | PNG → WebP q80 | ✅ 3.61 MB → 255 KB (93.1% smaller) |
| C4 | Already-compressed WebP → JPG | ✅ 305 → 156 KB. The code labels results that grow as "Larger than original" (honest). |
| C5 | Compress 32 MP / 17 MB | ❌ **Fails** (same as C2) |
| S1 | Portrait PNG → 2000² square, 8% padding, white | ✅ Visually verified |
| S2 | Transparent square | ✅ PNG with alpha |
| B1 | Bulk 20 × 1600×1200 → Amazon | ✅ 20/20 in **2.35 s**, ZIP holds 20 valid files |
| B2 | Bulk cancel mid-run | ✅ "Stopped. 4 processed"; partial ZIP available |
| B3 | 51 files | ✅ 50 accepted, clear message |
| B4 | >100 MB combined | ✅ 6 accepted, clear message |
| W1 | Text watermark | ✅ Visible, dimensions kept. ⚠️ Default white text is low-contrast on light photos (visual check). |
| W2 | Empty watermark text | ✅ "Enter watermark text first." |
| W3 | Logo watermark | ✅ Placed bottom-right |
| W4 | Invalid logo (TXT) | ✅ "Choose a PNG, JPG or WebP logo up to 5 MB." |
| BG1 | Background remover, no key | ✅ Button disabled; UI says "Service not connected"; direct POST → **503 `not_connected`** (server-side gate works) |
| — | Metadata | ✅ No EXIF in any output (matches the "Photo metadata is removed" claim) |

### Calculators and documents

| ID | Test | Result |
|---|---|---|
| P0 | Default currency | ⚠️ **USD** (audience hypothesis is Pakistan) |
| P1 | PKR 2500 − (1200+250+150+100+50) | ✅ Net PKR 750, margin 30%, markup 42.86% |
| P2 | Loss case | ✅ −PKR 300, −30% |
| P3 | Cost 7.333 (3 decimals) | ⚠️ Rejected with a vague "Enter a valid price and cost" (cause: `step=0.01`) |
| P4/P5 | Negative or empty | ✅ Rejected |
| I1 | Invoice 3 × 33.33 + 250, tax 17%, PKR | ❌ **BUG H1:** shows "PKR 33 / PKR 100 / Subtotal PKR 350 / Tax PKR 60 / **Total PKR 409**". The printed numbers do not add up (350 + 60 ≠ 409). Same in the PDF. |
| I2 | Logo / discount fields | ❌ Not present |
| I3 | Due date before issue date | ✅ Blocked with a clear message |
| I4 | Line items | ✅ Max 20, then the add button is disabled |
| I5 | Print / PDF | ✅ Print view shows only the invoice (form hidden) |
| U1 | URL with existing `utm_source` | ✅ Replaced, other parameters kept, spaces encoded |
| U2–U4 | Invalid, `javascript:`, credentials URL | ✅ All rejected |
| U5 | Urdu values | ✅ Correctly percent-encoded |

### Responsive, keyboard, errors

- **No horizontal page overflow** on any route at 360 / 390 / 768 / 1024 / 1440 px. Body text is 16 px.
- **No site JavaScript errors** on any route.
- ⚠️ On 360 and 768 px the studio tab strip scrolls sideways, so "Square maker" is cut off with no scroll hint.
- ⚠️ 2–4 interactive elements per page are under 32 px tall (small text links).
- ✅ Keyboard: logical tab order (skip link → nav → upload → tool tabs → marketplaces → fields) with visible focus rings.
- Bulk performance: 20 photos in about 2.4 s on this test machine. A low-memory phone was **not** tested (hypothesis: 50 × 20 MB batches may exhaust mobile memory; measure before advertising).

## 4. Findings by severity

| ID | Severity | Finding | Fix direction (Phase 05 unless approved sooner) |
|---|---|---|---|
| **H1** | High | Invoice displays PKR (and any currency whose `en` format has 0 decimals) rounded per line, so visible totals don't add up | Always format with 2 decimals, or round amounts to the displayed precision before summing |
| **H2** | High | Compressor and watermark accept 16–36 MP photos, then fail. Common for 20–50 MP cameras. | Add an explicit "limit longest side" option and say so before processing, or raise the output cap with a memory guard. Never shrink silently. |
| **H3** | High (external) | **remove.bg standalone service and self-serve API end on 1 Dec 2026 09:00 CET.** API moves to Leonardo.ai, and unused PAYG credits expire (first-party remove.bg FAQ and API page, read 2026-11-11). The current adapter will stop working. | **Do not buy remove.bg credits.** In Phase 10, build a provider adapter for a verified replacement (candidates: Leonardo.ai, or Photoroom API at $0.02/image) |
| M1 | Medium | Profit calculator: USD default; costs with >2 decimals rejected with a vague message | Default currency from settings; allow `step="any"`; precise message |
| M2 | Medium | Profit: no COD/courier charge, return-rate, packaging, payment % fee or tax; fees are a fixed amount only | Add fields; show formula and assumptions (spec) |
| M3 | Medium | Invoice: no logo, no discount, single tax line, nothing saved | Phase 05 (logo, discount, saved business details for logged-in users) |
| M4 | Medium | Watermark: 5 positions (spec wants 9), no tiling or spacing, low-contrast default | Phase 05 |
| M5 | Medium | Compressor: no target-size mode, no optional resize | Phase 05 (real encoding attempts, honest "target not reached") |
| M6 | Medium | Resizer "Gallery image" option behaves the same as "Main listing"; marketplace presets need sourced guidance | Phase 05; research official guidance before using the word "requirement" |
| M7 | Medium | Bulk: fixed filename pattern, sequential processing, no retry for failed files | Phase 05 |
| L1 | Low | Dead code: `views/image.php` and the `image` controller in `tools.js` (image routes now use the studio) | Remove in Phase 03 refactor, **with approval** |
| L2 | Low | Mobile tab strip cut off | Wrap tabs or add a scroll hint |
| L3 | Low | Width field max 4096 vs engine max 8192; only the native bubble explains it | Align limits; in-page message |
| L4 | Low (security) | `/wp-json/wp/v2/users` exposes the `admin` username | Phase 12 hardening |
| L5 | Info | Public pages are theme templates, not Elementor | Phase 03 (Elementor foundation) |
| L6 | Info | `/tools/product-image-tools/` not linked from the homepage | Phase 04 |

## 5. Processing location and privacy (truthful claims)

- 8 of 9 tools process **only in the browser**. No image upload to the server was observed for them (they use canvas and object URLs). The page wording "Your photo is processed in this browser" is accurate.
- The background remover **uploads the selected photo to the server and then to the provider** only when the user clicks "Remove selected background", and the UI says so. Today it is admin-only, with a daily cap and per-IP limits, and is not connected.
- The anonymous limits (50 files etc.) are browser-side UI limits. **They are not tamper-proof** and must not be described as enforced quotas.

## 6. Before/after feature inventory baseline (for Phase 05)

This audit is the "before" state. Phase 05 must re-run this matrix and show the "after" state per tool.
