# Competitor & Provider Research (Phase 01)

_Collected: **2026-11-11**. First-party pages were downloaded and read directly. Prices change; re-check before any decision. Secondary sources are labelled. **Observed evidence ≠ market proof.** Nothing here shows that demand exists or that a market is unserved._

## 1. Direct competitors studied

| Product | Source (first-party) | What it offers | Limits / price seen | Unknowns |
|---|---|---|---|---|
| **iLoveIMG** | iloveimg.com/pricing | Free web image tools: compress, resize, crop, convert, watermark, remove background, batch | Free: **30 files/task** (compress, resize, watermark), 200 MB/task, background removal 3. Premium: 120 files/task, 4 GB, "2.000 AI credits", **US$5/month billed annually or US$9 billed monthly**. Mentions "regional file processing", i.e. **server-side** processing. | Exact free daily caps; Urdu support (not seen) |
| **Photoroom (app)** | photoroom.com/pricing | AI product photos, backgrounds, batch, brand consistency, video | Pro: 8,000 AI credits and 1,000 exports/month; Max: 25,000 and 3,000; Ultra: 75,000 and 10,000. Page HTML contains $7.50, $20.99 and $82.50 ("per month, billed yearly"), but the **plan-to-price mapping was not verifiable** in static HTML. | Free tier details; PKR pricing |
| **Photoroom API** | photoroom.com/api/pricing | Remove Background API (Basic), Image Editing API (Plus) | "Basic price per image of **$0.02**"; a $20 Basic plan = 1,000 images; sandbox of 1,000 images/month and a 10-image free trial | Pakistan billing; output limits |
| **remove.bg** (current provider) | remove.bg/pricing, /faq, /api | Background removal app and API | PAYG: 3 credits $3 … 8,000 credits $1,699. Subscriptions: 40/month **$9** … 75,000/month $5,450. **Sunset: "From 1 December 2026 at 9:00am CET, remove.bg's standalone website will no longer be available… API access to Leonardo.ai… Any unused PAYG credits will expire on 1 December 2026."** Enterprise contracts unaffected. | Leonardo.ai pricing for the migrated API (not yet read) |
| **Wasooli COD Reconciliation** | apps.shopify.com/wasooli-cod-reconciliation | **Pakistan-specific** COD reconciliation: upload the courier payment file → match to Shopify orders; short payments, missing cash, aging per courier, WHT per courier per month | **$9.99/month**, 14-day trial. Supports TCS, Leopards, PostEx, Trax, M&P and DigiDokaan exports. **Launched 5 Nov 2026, 0 reviews.** | Shopify-only? (appears so); traction unknown |
| **Oshi Courier** | oshicourier.pk | Courier aggregator: booking, 14+ carriers, auto courier selection, Excel/CSV bulk booking with column auto-mapping, return alerts, team roles, WooCommerce/Shopify plugins, API | Claims COD settlement "every 48 hours — directly to your registered bank account" (vendor claim, unverified) | Pricing not shown; reconciliation depth |

**Other tools seen only in search results (secondary, not verified):** Click and Ship (Shopify, PK couriers + WhatsApp notifications), TrackKaro (WooCommerce, PostEx/Leopards), Courifire (Shopify booking), Flaship (courier app with pending-payment tracking).

## 2. What this means (observed vs hypothesis)

**Observed:**
1. Generic image tools are a **crowded, free-heavy** space. iLoveIMG gives 30 files/task free; Photoroom covers AI product photos. Our free tools must compete on **privacy (local processing), marketplace presets and simplicity**, not on raw features.
2. Background removal at scale costs about **$0.02/image** (Photoroom API, first-party). A "credits" add-on is economically possible; a free unlimited version is not.
3. The **current background-removal provider is shutting down its self-serve API on 1 Dec 2026.** This is a hard technical dependency for Phase 10.
4. **COD/courier reconciliation for Pakistani sellers already exists** (Wasooli, launched 6 days ago; Oshi Courier settlement features). The proposed Seller Desk differentiator is **not** unserved.
5. The tools found are mostly tied to **Shopify/WooCommerce stores or a specific courier**. No first-party evidence was found of a tool aimed at **WhatsApp/Instagram sellers without a store**. That is an absence of evidence, not proof of a gap.

**Hypotheses to validate (do not market these as facts):**
- H-A: WhatsApp/Instagram sellers without a Shopify store need courier-payment tracking that works from CSV alone.
- H-B: Urdu/English step-by-step guidance improves completion for these sellers.
- H-C: Sellers will pay for saved work (presets, brand kits, invoice details) rather than for one-off image edits.
- H-D: Mobile-first workflows matter more than desktop batch power.
- H-E: Sellers would trust a tool that does **not** upload their photos (local processing) more than a server-based one.

## 3. Sources (accessed 2026-11-11)

- https://www.iloveimg.com/pricing
- https://www.photoroom.com/pricing
- https://www.photoroom.com/api/pricing
- https://www.remove.bg/pricing · https://www.remove.bg/faq · https://www.remove.bg/api
- https://apps.shopify.com/wasooli-cod-reconciliation
- https://oshicourier.pk/
- Secondary (search summaries, not verified): alternativeto.net news on the remove.bg shutdown (Sept 2026); photoroom.com/blog/remove-bg-shutting-down; Shopify App Store listings for Click and Ship and Courifire.
