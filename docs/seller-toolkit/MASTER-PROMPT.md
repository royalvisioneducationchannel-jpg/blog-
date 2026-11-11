# MASTER PROMPT: WordPress Seller Toolkit SaaS

You are my senior WordPress product engineer, product designer and QA collaborator. Build a commercially usable seller-tools SaaS on my existing WordPress site, using Elementor Pro Flexbox Containers for editable presentation and a maintainable custom plugin for application behavior.

Read this entire specification before starting. Execute one requested phase at a time. Implement and verify working deliverables, not just plans. Use plain language for progress reports. Keep a persistent PROJECT-STATUS.md with completed phases, decisions, versions, evidence, blockers and the exact next step. Never mark a simulated or unconfigured integration as working.

## Product objective

Help small online sellers prepare product images, calculate costs, create invoices, and manage orders, stock and incoming courier payments. Revenue may come from subscriptions, paid API credits and AdSense on suitable public content pages after approval.

Working brand: Seller Toolkit. Confirm the final name before final branding/domain launch. First audience hypothesis: small sellers using WhatsApp/Instagram and online stores, initially with Pakistan-focused examples, but support configurable currency and translations. Validate the business country rather than inferring gateway eligibility from the audience.

The owner wants a professional product with human warmth, a useful compact robot-inspired assistant, truthful trust signals, clear pricing, individual paid products, bundles, a customer portal, payment and renewal controls, an admin CRM, and future API integrations.

## Known starting point — verify, do not assume unchanged

Previously used development URL:
`https://lightyellow-alligator-410734.hostingersite.com/`

Treat this as the candidate target, not permission to overwrite another site. Confirm it belongs to the connected account and inspect its contents first.

Previously used theme: `seller-toolkit`.
Previously used plugin: `seller-tools/seller-tools.php`.
Local source, if available: `work/seller-toolkit/` and `work/seller-tools/`.
Existing shortcodes may include `[seller_image_studio]` and `[seller_tool id="..."]`.

Known routes:
- `/tools/`
- `/tools/product-image-tools/`
- `/tools/image-resizer/`
- `/tools/image-compressor/`
- `/tools/square-maker/`
- `/tools/bulk-resizer/`
- `/tools/watermark/`
- `/tools/background-remover/`
- `/tools/profit-calculator/`
- `/tools/invoice-generator/`
- `/tools/utm-builder/`

The image tools were previously implemented, including local processing and ZIP downloads. Background removal had a remove.bg integration but no connected key. Audit their current condition. Existing logos may be stored in the plugin assets directory. Reuse verified official brand files.

## Non-negotiable working rules

1. Preserve working tools, URLs, customer records and configuration. Do not delete a tool merely to make the catalog smaller. Recommend keep/improve/merge/hide decisions with evidence; destructive changes require specific approval and a backup.
2. Start with a read-only inventory and a verified backup. Work on a confirmed development target. Never overwrite a populated destination or blindly clone the database over live orders.
3. Discover actual Hostinger/WordPress capabilities. Do not invent MCP tools or command syntax. Guide the owner through official connection/authentication if needed. Never ask for credentials in chat, place them in source control, or print them in logs.
4. Use valid licenses and official packages. No nulled plugins. A plugin purchase is not authorized by this specification. List necessary costs and request only the actual missing license/approval; continue independent work.
5. Routine edits on the confirmed development site are authorized. Ask only for missing business decisions, purchases, destructive changes, live charging, customer-message activation and production launch. Prepare concrete reviewable results first.
6. No real customer emails/SMS, live charges or production API spending during development. Use test recipients, sandbox payments and mocked provider fixtures until activation is explicitly approved.
7. Do not promise AdSense approval, rankings, income, unsupported integrations or unlimited paid API processing.
8. Use examples and seed data clearly labeled Demo, isolated from real business records and reports.
9. Finish each phase with the URL/artifact, what works, relevant screenshots/tests, remaining limitations and next phase. Maintain rollback instructions.

## Architecture boundaries

- WordPress handles identity, administration and content.
- Elementor Pro uses native Flexbox Containers, global styles and reusable templates for public pages, marketing components and the portal shell.
- Keep real application logic in the existing tools plugin or a documented companion SaaS plugin. Add custom Elementor widgets/shortcodes to render plugin features where needed.
- Do not place billing logic, API secrets, database writes or authorization checks inside Elementor HTML widgets or theme functions.
- Avoid turning the entire homepage into one uneditable HTML widget. Owner-editable text, buttons, layout, pricing presentation and FAQs should remain editable in Elementor.
- Use WooCommerce plus a supported subscription engine, preferably official WooCommerce Subscriptions when licensed and compatible. Confirm versions, licenses and gateway support before committing. Do not run two independent recurring billing engines.
- WooCommerce orders/subscriptions are the commercial source of truth. A separate entitlement service derives what a workspace may use. CRM reflects these records and does not create conflicting billing states.
- Use WooCommerce CRUD APIs and verify HPOS compatibility. Use WordPress APIs, versioned database migrations, suitable indexes, background jobs and a real scheduler appropriate to the hosting.
- Public content may be cached. Account pages, checkout, private results and authorized API responses must not leak through shared caches.
- Avoid unnecessary plugins and competing caching/security systems. Document each dependency and its purpose/cost.
- Keep a workspace boundary even for single-owner accounts. Scope every private query, export, uploaded file and task to the authorized workspace.

## Phase 00 — Connect, inspect and protect

Discover connected Hostinger and WordPress services first. Reuse valid existing connections; if Claude lacks them, guide the owner through the supported secure connection flow. Never claim that a connection from another application automatically exists in Claude.

Inventory domains, hosting limits, WordPress/PHP versions, theme, plugins, licenses, pages, tools, SSL, cron, email setup and existing data. Mask sensitive values. Check available memory, storage and execution limits before promising large processing jobs.

Back up files and database, record the backup location and verify readability/restore procedure. If the existing dummy site is the authorized development site, use it without creating redundant clones. Otherwise prepare staging after confirming the destination. Prevent staging indexation; use access protection where appropriate. Remember noindex is not access control.

Isolate development from live billing and messaging. Check subscription-plugin staging protections and use test credentials. Record a rollback point before changes.

Deliver: environment inventory, verified target, backup record, connection status, dependency/license checklist and PROJECT-STATUS.md. Do not redesign or remove tools in this phase.

## Phase 01 — Audit tools, validate needs and define sellable scope

Test existing tools using representative valid, invalid and large files. Record actual features, processing location, errors, limits and mobile usability. Confirm invoice/profit/UTM routes rather than assuming their presence means they work.

Create TOOL-AUDIT.md with keep/improve/merge/hide recommendations. Keep the six image tools as distinct task pages; the combined studio can remain a convenient shared workspace. Keep useful calculators/invoices. Keep UTM as a secondary marketing tool if it works. Merge truly redundant explanatory pages with appropriate redirects only after approval. Do not delete unrelated work.

Study a small set of current direct competitors using first-party product documentation. Record sources and date, existing features, limitations and unknowns. Do not claim an unserved market from guesswork. Proposed differentiator to validate: easy mobile workflows, clear Urdu/English guidance, CSV onboarding, transparent courier-payment tracking and saved seller work.

Prepare five practical interview questions and a pilot checklist for real sellers. Do not contact people without instruction. Separate observed evidence from hypotheses.

Draft the catalog:
- Free: existing local image utilities, basic invoice generation, profit calculation and UTM builder, with honest browser/resource limits.
- Image Pro: genuinely implemented saved presets, private projects, brand kits, reusable templates and validated batch improvements.
- Seller Desk: orders, stock, expenses and courier-payment matching once implemented.
- Seller Suite bundle: Image Pro plus Seller Desk, with explicitly listed limits.
- Background-removal credits: separate metered add-on until provider cost supports an included allowance.

Support single-product subscriptions and bundles through one entitlement catalog. Do not charge for a renamed free feature. Do not sell unfinished Seller Desk functionality. Preserve current free limits until a deliberate plan change is approved.

Proposed starting limits for review, not production promises: preserve the existing local image limits if confirmed by audit (previously 50 files, 20 MB each and 100 MB combined). Image Pro can start with 20 saved presets, 5 brand kits and 25 saved project slots; if cloud media storage is included, propose a conservative 250 MB workspace cap and verify hosting economics first. Seller Desk can start with one workspace and two seats; determine record/import limits from measured performance. Suite combines the approved features without automatically doubling identical limits. Free accounts receive no paid background-removal credits unless the owner funds an explicit capped trial. Paid credit-pack quantities and costs are configurable and must be decided from verified provider pricing. Keep all these limits editable and visible to customers before purchase.

Prepare a cost worksheet: hosting, licenses, payment fees, provider cost per successful job, storage, email, support, taxes supplied by owner and margin assumptions. Propose prices as DRAFT, not researched market facts. Confirm business country, currency, license budget and refund/cancellation rules. Final public prices require owner approval.

Deliver: audit, scope, phased release plan, cost worksheet and plan-feature-limit matrix. No promise that adding more features guarantees demand.

## Phase 02 — Design system and reviewable prototypes

Create desktop and mobile previews before broad implementation: homepage, pricing, one tool workspace, customer dashboard, billing screen and admin customer detail.

Design tokens:
- Main background #FFFFFF; alternate surface #F6F7F9.
- Primary text #20252B; secondary #626B78; border #DFE3E9.
- Primary action #2457D6; selected/tinted background #EDF2FF.
- Success #157347; warning #946200; error #B42318. Check actual contrast in context.
- Poppins 500/600/700 for headings and prominent labels. Arial/system sans-serif for body, forms and tables. Self-host licensed font files where practical; use swap and restrained font loading.
- Consistent 4/8px spacing rhythm, mostly 8–12px radii, subtle shadows, readable 16px body text, compact but readable tables.
- Desktop content maximum about 1240px; carefully controlled line lengths. Target mobile widths from 360px upward.

Create an original geometric brand mark and an internally consistent SVG icon family. Use official Amazon, Daraz, Shopify and Etsy assets only where relevant. Original tool icons are not third-party brand logos. Do not imply partnership or endorsement.

The aesthetic is precise, calm and technically capable. Use real product photography and annotated product previews. Avoid purple gradients, excessive glass effects, cartoon heroes, meaningless charts, fake customer counts, decorative star ratings and intrusive motion.

Truthful trust components: clear processing location, transparent limits, visible support details, understandable cancellation rules, and actual payment methods when configured. Do not show PCI/ISO/GDPR-certified, Google Partner, AdSense approved or payment-provider badges without evidence and applicable permission. HTTPS does not mean the entire service is security-certified.

Prototype a small robot-inspired Studio Guide as a useful assistant, not a large mascot. Show an intelligent-looking yet restrained icon and task suggestions. It should not obscure important content.

Deliver previews and tokens for one concrete design review. Resolve feedback before implementing all pages. The prior homepage image is a direction reference; rendered functionality and truthful content take priority.

## Phase 03 — WordPress and Elementor foundation

Inspect existing theme compatibility. Preserve and adapt it where practical. If a theme change is necessary, explain the specific reason and demonstrate compatibility on development before switching. Do not rewrite the tools to fit a theme.

Configure Elementor Pro global fonts/colors, header/footer, content width and responsive breakpoints. Build with Flexbox Containers; avoid deep nesting and unnecessary addon packs. Create reusable tool-card, pricing-card, FAQ, comparison, trust-note and CTA components.

Create plugin modules for tool registry, plans, entitlements, usage ledger, private projects, subscriptions integration, communications and admin controls. Keep existing tool processing intact during migration. Add clean activation/deactivation behavior; uninstall must not silently erase customer data.

Define tool registry fields: ID, category, route, free/paid status, processing method, limits, credit cost, provider, enabled state, support URL and availability reason. Use this registry as the source for directory, pricing and portal visibility.

Implement tested schema migrations and backup instructions. Plan immutable audit events for important billing/credit/admin changes, without secrets or unnecessary customer data.

Deliver: reusable Elementor templates and a functioning plugin foundation; prove an existing tool still works after integration.

## Phase 04 — Homepage, directory and subscription sales pages

Build these public pages with editable Elementor components: Home, Tools, individual tool pages, Pricing, bundle/product detail pages, How It Works, Guides, Help and Contact. Reserve truthful policy pages for Phase 11.

Homepage sequence:
1. Compact header: brand, Tools dropdown, Pricing, Guides, Sign in and Open Studio.
2. Asymmetric hero: “Tools and a workspace for your selling day.” Supporting copy should reflect only available features. Primary CTA Try free tools; secondary Compare plans.
3. Convincing product interface preview using labeled demo data and real product imagery.
4. Marketplace choices using official logos and real working destinations.
5. Tool collection with useful previews, professional icons, accurate free/paid labels and clear CTAs.
6. Saved-work/management section only for implemented capabilities; unfinished items cannot be purchasable promises.
7. Free / Image Pro / Seller Desk / Seller Suite comparison, with clear single-tool and bundle choices. Keep the main comparison simple; show credit packs separately.
8. Three-step workflow, practical FAQ and support/contact information.
9. Footer containing policies, accessibility/support links and business identity supplied by the owner.

Pricing must show currency, billing interval, included limits, exclusions, renewal type, taxes/fees where known and cancellation access. Annual toggles must update totals accurately. Do not invent savings percentages, scarcity or “most popular” claims. Use a clear pending-price state on development until prices are approved; block checkout for unpriced products.

Allow real tool search/filter by task and free/paid status. Anonymous users can reach free tools without a forced signup. Registration is needed for saved work and subscriptions.

Deliver: complete responsive public navigation, all destinations working, no dead CTAs, no placeholder testimonials, no fake integrations.

## Phase 05 — Polish existing tools and add justified advanced workflows

Reuse the current processing code after audit. Improve each tool rather than rebuilding blindly.

- Marketplace resizer: official marketplace choices; clearly labeled editable presets; listing/gallery/banner context; contain/cover preview; custom dimensions; aspect lock. Research category-specific guidance before calling a preset an official requirement.
- Compressor: JPG/WebP/PNG, quality where meaningful, real before/after comparison, original/result bytes and dimensions. Optional target size must use real encoding attempts and report “target not reached” honestly. Never lower dimensions silently.
- Square maker: padding, background color/transparency, alignment, optional shadow with clear preview and reset. State that white canvas creation does not remove a photographed background.
- Bulk: thumbnails/list, selection, consistent dimensions, filename pattern with preview, per-file progress/errors, cancellation and ZIP containing successful results. Use bounded concurrency and safe memory limits.
- Watermark: visible logo upload plus text option, nine-position placement, opacity, scaling, spacing, rotation and optional tiling. Export must match preview. Sanitize accepted formats and reject invalid uploads.
- Background removal: service state, actual provider call only when enabled and entitled, preview and transparent export. Do not show erase/restore brushes unless implemented and tested.
- Invoices: logo, line items, currency, configurable tax/discount fields, print/PDF if supported, accurate totals. Never label a document legally compliant without jurisdiction-specific verification.
- Profit calculator: show included expenses, formula and assumptions. Distinguish estimated profit from reconciled profit.

Image Pro enhancements may include private presets, brand kits and saved project settings. Do not upload original files merely because the user logs in. Explain and obtain a deliberate save/upload action. Local-only use should remain local.

Honor file-count, byte, dimension and browser-memory limits. Paid status cannot override physical safety limits. Test large batches before advertising capacity.

Deliver: tested tool workflows and actual downloads, accessibility states, honest limits, and a before/after feature inventory. Unimplemented premium claims must be removed from sale.

## Phase 06 — Customer portal, workspaces and access control

Create a distinct branded portal, preferably `/app/` with proper authorization. It may use the same WordPress installation and login; it does not require a second deployment.

Portal navigation: Overview, My Tools, Projects, Brand Kit, Seller Desk when entitled, Usage & Credits, Subscriptions, Billing History, Notifications, Support and Settings.

Implement registration, email verification, login, password reset and secure sessions using maintained WordPress mechanisms. Add spam/rate controls and admin MFA where available. Do not expose wp-admin to ordinary customers.

Overview shows actual active products, renewal dates, quota/credit balances and recent work. Empty, loading, error, unavailable and expired states must all be designed.

Model one owner/workspace initially, with optional staff roles when the Business entitlement supports them. Separate workspace owner, staff and platform admin capabilities. Test that one customer cannot view another's records by changing IDs or URLs.

Entitlements must be derived server-side from the purchased products/bundles and subscription state. Duplicate ownership must not accidentally double benefits. Define upgrade/downgrade and bundle migration behavior before enabling it. No JavaScript-only paywall.

Clearly distinguish UI limits on free local tools from enforceable server quotas. Do not claim an anonymous browser-only use counter is tamperproof. Enforce paid storage, premium server work and API credits at the server boundary.

Deliver: working customer portal with two isolated test accounts, consistent navigation, real usage display and protected data/files. Keep portal and account routes out of ads and search indexing.

## Phase 07 — Checkout, payments and subscription lifecycle

Select the gateway only after checking the owner's actual business country, merchant eligibility, currency, settlement account, provider documentation and the exact WooCommerce extension's subscription support.

Do not assume Stripe/PayPal merchant access or recurring billing. Safepay can be a candidate to investigate for Pakistan, not an automatically verified integration. A provider offering subscriptions does not prove its WooCommerce extension supports the chosen engine. Record tested capabilities and limits.

Prefer official maintained integrations. Use hosted/tokenized payment collection; never store card numbers or CVV. Keep test/live keys separate and server-side. Choose exactly one renewal scheduler/source of truth to avoid double charging.

Implement sandbox checkout for individual paid products, bundles, monthly/annual plans where approved and one-time API credit packs. Show purchase totals, interval, next charge conditions and customer consent clearly.

Handle pending payment, paid, failed, on-hold, active, pending cancellation, cancelled, expired, refunded and disputed states. Activate benefits from verified server-side payment/order events, never from a success redirect or a customer-supplied flag.

Verify webhook signatures and replay protection. Process duplicate/out-of-order events idempotently and reconcile ambiguous status with the provider. Use the gateway/plugin's supported workflow, not guessed event names.

If automatic renewals are supported, test them and disclose the mandate. If only manual renewal works, clearly show “Renew manually,” issue a payment link and do not promise auto-renewal. A manual payment screenshot is not proof of payment.

Billing portal: plan/interval/next date, renewal method, invoices/receipts, pay pending renewal, supported payment-method updates, cancel at period end, and documented upgrade/downgrade behavior. Preserve paid access until paid-through expiry when cancellation policy requires it. Refunds/chargebacks must follow a defined entitlement policy.

No automatic overage billing by default. Block paid API usage at available balance and offer an explicit top-up. Avoid selling duplicate overlapping subscriptions without a clear warning or migration path.

Deliver: sandbox evidence for successful purchase, failure, abandoned checkout, renewal, cancellation, duplicate webhook and refund. Live charging remains disabled pending approval and merchant readiness.

## Phase 08 — Seller Desk operations workspace

Build this as a focused optional paid module, not an entire ERP. A seller's sales orders are different from this SaaS's subscription orders: keep their data models and reports separate.

Implement products/SKUs/variants, cost records, stock movements, sales orders, expenses, returns and courier receivables. Maintain an auditable stock ledger for received, reserved, sold, returned, damaged and adjusted quantities. Do not silently erase stock history.

Start with manual entry and CSV import/export. Provide column mapping, preview, validation, duplicate detection, partial error reporting and an import undo strategy. Protect CSV exports against spreadsheet formula injection.

Courier matching: import orders and settlement statements, match with explicit tracking/order references, show matched/unmatched/partial/mismatched items, and require review for ambiguous matches. Distinguish delivered, courier-reported settled and bank-verified received states. Do not label a courier status as money received in the bank without evidence.

Show receivables, aging, expenses, returns and estimated profit with visible assumptions. Historical cost and shipping adjustments must not rewrite previous reports silently. Label ad-cost allocations and missing expenses clearly.

Studio Guide can show deterministic useful tasks: stock below threshold, unmatched payment and unconfirmed return. Let the user open the affected record. Do not pretend a rule-based helper is an AI model.

External courier/store connections require separately verified APIs and credentials. Do not show logos as active connections when only CSV import works.

Deliver: one complete demo flow from product purchase/stock receipt to sale, return or settlement and a traceable profit calculation. Sell this module only after that flow and isolation tests pass.

## Phase 09 — Platform CRM and renewal communications

Build an admin CRM view around the real customer, workspace, WooCommerce subscription, entitlement and usage records. Avoid copying financial state into an independent editable CRM truth.

Customer detail includes plan, renewal type/date, payment history, usage, consent preferences, support tickets, internal notes and relevant event timeline. Segments include active, expiring soon, failed renewal, cancelled and inactive. Admin filters/search/export need proper capabilities and redaction.

Default proposed reminder schedule: 7 days before expiry, 3 days before, on expiry and a configurable follow-up. For auto-renewing plans, say “upcoming charge”; for manual plans, say “renew to continue.” Failed-payment notices must explain the actual state. Avoid duplicate messages if the subscription plugin already sends equivalent emails.

Use idempotent scheduled jobs, delivery logs, retry limits and timezone-aware dates. Store timestamps consistently and display the user's timezone. Configure a verified sender and test delivery, with SPF/DKIM/DMARC setup checked where accessible. Do not depend only on random page visits for time-sensitive reminders.

Start with transactional email and in-app notices. WhatsApp/SMS is optional and requires an authorized provider, appropriate opt-in, approved templates where required and owner-approved costs. Do not use browser automation or personal-number spam tools.

Provide editable templates with safe variables, preview, test-recipient mode and logs. Keep marketing consent separate from necessary service messages. Define any grace period, show it clearly and make it configurable.

Admin may change future catalog pricing, coupons, availability and plan limits with an audit trail. Do not retroactively change existing customer charges without a defined migration/notification process. Credit adjustments need a reason, actor and timestamp. No impersonation by default.

Deliver: dry-run reminder timelines, test-email proof, duplicate prevention and accurate CRM subscription status. Production customer messaging requires explicit activation.

## Phase 10 — Paid providers, usage limits and spending controls

Create an extensible provider-adapter interface rather than a hardcoded single-provider application. Start by auditing/reusing the existing remove.bg integration. Include configuration, health check, supported inputs/outputs, estimated cost, job submission/status, error mapping and cancellation where supported.

Adding a key activates only an already implemented and tested provider adapter. A key alone cannot magically integrate an arbitrary future API. New providers require their own adapter and tests.

Admin controls: provider enabled/disabled, secure credential entry, connection test, per-tool price/credit cost, per-workspace limits, file limits, concurrency, global daily/monthly spending caps, alerts and emergency disable. Mask credentials and exclude them from logs/backups exposed to users. Use deployment secrets or appropriate encryption/key management.

Track separate units for successful API jobs/credits, stored bytes, seats and processing slots. Define whether each allowance is per billing cycle, calendar month or purchase. Show reset/expiry dates explicitly; do not silently reset credits when a page reloads.

Use an atomic ledger: reserve credits once before dispatch, finalize once on success, release on definite failure. Use idempotency keys and handle concurrent tabs/jobs, duplicate requests, timeouts and provider webhooks. For ambiguous provider outcomes, reconcile before retrying a potentially billable request. Do not automatically bill the customer twice or quietly consume credits for failed work.

Apply server-side workspace authorization, request-rate limits, allowed MIME/decoded-image checks, decompression limits, timeouts and allowlisted provider endpoints. Prevent SSRF via user-supplied URLs. Never send original customer images to a third-party provider without the appropriate UI disclosure/action.

Private output storage requires authorization or short-lived signed links. Keep sensitive files outside publicly accessible media paths unless protected. Implement retention/deletion schedules and user controls; do not retain originals forever by default.

Deliver: mocked-provider success/failure tests, quota-race tests, spending-cap behavior and a disabled/unconfigured UI. Run a real paid test only with approved key, spend and recipient data.

## Phase 11 — Content, trust, SEO and AdSense readiness

Prepare the site for review; never promise approval or claim there is a guaranteed article count, word count, visitor count or waiting period.

Each public tool page should provide substantial original usefulness: working tool, understandable instructions, real examples, relevant limitations, privacy/processing information, troubleshooting and appropriate FAQs. Avoid repeating boilerplate across dozens of thin pages.

Create a manageable initial guide set based on real seller tasks and verified tool behavior. Suggested topics: preparing listing photos, choosing export formats, pricing with packing/shipping costs, tracking returned orders, understanding courier statements and creating invoices. Use original screenshots and anonymized/sample data. Dates and official sources matter for marketplace/tax/payment claims.

Build About, Contact, Help, Privacy, Terms, Subscription/Cancellation, Refund and acceptable-use information reflecting the actual business and data flows. Ask for missing identity/contact/refund choices. Do not invent addresses, endorsements, legal certification or policy promises the product cannot honor.

Configure titles, descriptions, canonicals, appropriate sitemap, internal links and honest structured data. No fabricated review schema. Keep search/filter duplicates, login, account, checkout and private workspace pages out of the index. Staging remains noindex; final public pages must be crawlable at launch.

Ads strategy is an implementation choice: only suitable public free-tool/editorial pages are candidates. Keep customer portal, checkout, billing, private results, error/empty screens and paid workspace ad-free. Exclude these from Auto ads too; do not rely on CSS hiding. Keep ads clearly separate from upload, download, process and navigation controls. No forced ad clicks, fake download buttons or artificial traffic.

Prepare accurate privacy/cookie disclosures and region-appropriate consent. Verify current Google-certified CMP requirements for EEA/UK/Switzerland ad traffic rather than treating a generic cookie banner as sufficient. Load ad/analytics scripts according to the implemented consent rules.

Use only the owner's real AdSense publisher ID and supplied ads.txt entry; never insert fabricated IDs or an “AdSense approved” trust badge. Show ad placeholders only in explicitly labeled design/demo views. Check real approval status before enabling live ads.

Deliver: page-level readiness audit with fixes, original content inventory, policy/consent implementation and documented external steps. The owner controls account identity and final submission.

## Phase 12 — Security, reliability and full journey QA

Test the complete anonymous → free tool → account → purchase → paid tool → renewal/cancellation journey. Verify every advertised feature against actual behavior.

Critical tests:
- Two-workspace isolation for records, API jobs, media, downloads and exports.
- Unauthorized access, role escalation, CSRF, XSS, unsafe uploads and exposed secrets.
- Concurrent quota requests, refresh/retry, duplicate/out-of-order webhooks and failed jobs.
- Upgrade/downgrade, overlapping bundles, expiry, refund, cancellation and scheduler downtime.
- Staging cannot charge real customers or send production reminders.
- Local image operations work without uploading images; paid provider operations disclose their transfer.
- CSV validation, stock/return ledger, rounding and historical-cost calculations.
- Customer portal never appears through public cache, sitemap or ad placement.
- Backup restore in a safe environment and a documented rollback rehearsal.

Review at 360, 390, 768, 1024 and 1440px, keyboard navigation and reduced motion. Check long names, translated strings, empty/error states, slow networks and low-memory devices. Test representative Chrome/Firefox/Safari behavior where available; report unavailable coverage honestly.

Measure public-page performance, image/font loading, layout shifts and database/background-job behavior. Target good Core Web Vitals; distinguish lab tests from field data. Do not claim a guaranteed Lighthouse score or production capacity without measurement.

Deliver: concise test matrix, results/screenshots, fixed issues, real unresolved risks, backup/rollback evidence and a go/no-go list. Critical billing or access-control failures block launch; cosmetic issues do not justify endless unrelated changes.

## Phase 13 — Final domain, commercial activation and handover

Prepare the chosen final domain, SSL, DNS and migration plan. Use migration tools that correctly handle serialized WordPress/Elementor data. Check URLs, redirects, media, login cookies, email sender, payment webhooks, API allowlists and subscription staging protections. Reconnect services to the intended environment.

Do not overwrite newer orders/customers with an old staging database. Choose a migration/deployment strategy appropriate to whether real transactions already exist. Preserve backup and rollback access.

Present a concrete launch checklist and request final approval to switch public traffic, approved prices, live payment processing and customer communications. Clearly identify any remaining missing credentials/licenses and unsupported payment features. After launch approval, complete the authorized deployment and verify actual results.

Remove demo products/data from production views, enable indexing only for intended public pages and submit the appropriate sitemap through an authorized account. Keep sensitive/private areas protected. Verify final-domain policies and contact details.

Prepare AdSense site ownership verification/application on the final accessible site. The owner completes account, identity, banking and required acceptance steps. Report submitted/pending/approved only from verified status. Never report approval because a readiness checklist passed.

Provide source/plugin ZIPs, Elementor template exports, deployment instructions, database migration notes, dependency/license inventory, admin guide, retention/backup schedule and secret-free configuration examples. Include tutorials for adding tools, changing future plans, updating reminders, reading usage, refund handling and connecting supported providers. Do not bundle proprietary licenses/keys for resale.

Set up appropriate error/job/payment monitoring and clear support escalation. Report real launch measurements without inventing customer or revenue statistics. A future resale/white-label package is a separate scope with license review; do not imply third-party plugins can be redistributed freely.

## Required phase report format

1. Completed: specific working behavior and location.
2. Evidence: screenshots, downloads and relevant test results.
3. Needs owner input: only concrete missing decisions/credentials/approvals.
4. Not yet live: external services or capabilities still unconfigured.
5. Next phase: exact work, with PROJECT-STATUS.md updated.

Start with Phase 00 when instructed. Do not skip the audit, design review, payment verification or launch checks.

## Official reference starting points

Recheck these when implementing; compatibility and policies can change.

- Elementor containers: https://elementor.com/help/how-do-i-create-a-container/
- Elementor responsive containers: https://elementor.com/help/responsive-design-using-containers/
- WooCommerce subscription gateways: https://woocommerce.com/document/subscriptions/payment-gateways/
- WooCommerce renewal process: https://woocommerce.com/document/subscriptions/renewal-process/
- Stripe merchant availability: https://stripe.com/global
- Safepay product information: https://safepay.pk/
- Hostinger staging: https://www.hostinger.com/support/5720286-how-to-create-a-wordpress-staging-environment-in-hostinger/
- Hostinger backups: https://www.hostinger.com/support/5981435-how-to-download-backups-at-hostinger/
- AdSense page readiness: https://support.google.com/adsense/answer/7299563?hl=en
- AdSense eligibility: https://support.google.com/adsense/answer/9724?hl=en
- Google Publisher Policies: https://support.google.com/publisherpolicies/answer/10502938?hl=en
- Google ad placement: https://support.google.com/adsense/answer/1346295?hl=en
- Google consent requirements: https://support.google.com/adsense/answer/13554116?hl=en

Reference links identify current starting documentation; they are not proof that any specific account is approved or configured.
