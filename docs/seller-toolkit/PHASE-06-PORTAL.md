# Phase 06 — Customer Portal, Accounts & Access Control (dev site)

_Completed 2026-11-11. Backup **R5** was taken first (`~/seller-toolkit-backups/2026-11-11-R5-pre-phase06/`). Plugin is now **seller-tools 1.6.2**._

## 1. What exists

**Portal at `/app/`** (Elementor page with the site header and footer, rendered by the plugin; customers never see wp-admin):
Overview · My Tools · Projects · Brand Kit · Usage & Credits · Subscriptions · Billing History · Notifications · Support · Settings · Sign out.
Each section shows **real data or an honest empty / not-available state**: Free plan; Image Pro features "not on sale yet"; credits "not available yet"; no payments; support "coming soon".

**Accounts (WordPress users, role `st_customer` = `read` only):**

| Flow | Implementation |
|---|---|
| Register | Email + password (min 10) + optional name. **Honeypot**, **signed minimum-fill-time (3 s)**, **10 per hour per IP**. Creates an owner **workspace**. Audit events `account.registered` and `workspace.created`. |
| Email verification | 32-char token, stored only as an HMAC, **48 h expiry, single use**. Unverified users see a banner with "Send a new link" (3/hour). |
| Login | `wp_signon` by email. **Per-account limit: 5 failed attempts / 15 min, whatever the IP** (blocks IP-rotating attacks), plus a generous per-IP limit (20/15 min) so shared mobile/CGNAT IPs don't lock out real users. Generic error, so accounts can't be enumerated. |
| Password reset | WordPress core `retrieve_password()` (maintained mechanism). **Same reply for known and unknown emails.** 5/hour per IP and 3/hour per email. |
| Sign out | Nonce-protected link. |
| wp-admin | Customers are redirected from any wp-admin page to `/app/`; the admin bar is hidden. |
| Settings | Name, **marketing consent** (default off, audited when changed); service emails are always sent. |

**Workspace boundary.** The workspace is **always derived from the signed-in user, never from the request**. REST: `GET /seller-tools/v1/me`, and `GET /seller-tools/v1/workspaces/{id}` (membership required). Responses are `no-store, private`.

**Caching, indexing, ads.** `/app/*` sends `Cache-Control: no-store, no-cache, must-revalidate, private`, `X-Robots-Tag: noindex, nofollow` and a robots meta `noindex`, and is excluded from the sitemap. No ads anywhere in the portal.

**Test-mail mode (owner instruction: "test emails use karo").** `ST_Mail_Guard` short-circuits **every** `wp_mail()` on the site while `seller_tools_mail_mode` = `test` (the default). Nothing is delivered; the last 30 messages are kept in an **admin-only Mail log** (WP Admin → Seller Toolkit → Mail log). Switching to `live` requires owner approval and a configured sender (Phase 09).

**Header:** "Sign in" menu item → `/app/`; it reads **"My account"** when signed in.

## 2. Evidence (Playwright + server checks, two isolated test accounts)

Test accounts (example.com, undeliverable): **A** = user 3 / workspace **#4**, **B** = user 4 / workspace **#5**. One earlier debug account exists (user 2 / workspace #3).

| # | Test | Result |
|---|---|---|
| A0 | Anonymous `/app/` | Sign-in form; `no-store` headers; `X-Robots-Tag: noindex, nofollow`; menu shows "Sign in" |
| A1–A3 | Too-fast submit / honeypot / short password | All rejected with clear messages |
| R | Register A and B | Accounts created, workspaces #4 and #5, verify banner, **no admin bar** |
| V1 | B opens a verify link carrying A's token | **Rejected** |
| V2 / V3 | A verifies; link reused | Verified, banner gone / **reuse rejected** |
| I1 / I2 | A `GET /me`, `GET /workspaces/4` | 200, own data |
| **I3** | A `GET /workspaces/5` (B's) | **403** |
| I4 | Anonymous `GET /workspaces/4` | **401** |
| I5 / I6 | A adds `?workspace=5` / `?workspace_id=5` to portal URLs | Still shows **#4** (request IDs ignored) |
| I7 | B's portal | Shows #5; still unverified |
| I8 / I9 | Customer opens `/wp-admin/`, `profile.php` | Redirected to `/app/` |
| S1 | Save name + marketing consent | Saved; audit `consent.marketing.changed` |
| L1 / L2 | Sign out / sign in again | Works; menu shows "My account" |
| P1 | Reset for B and for an unknown email | **Identical message** (no enumeration); reset email captured in the mail log |
| L3 | 6 wrong passwords for B, then the correct one | 6th → "Too many attempts"; **correct password also blocked during lock**; **A unaffected** |
| M1 | Mobile 390 px overview | 0 horizontal overflow |
| Mail | Mail log after the tests | 4 messages to example.com addresses, **mode = test, none delivered** |

## 3. Bugs found and fixed during the phase

1. **Field named `name`.** WordPress treats a POSTed `name` as a public query var (post slug), so a sign-up that included a name never reached the portal handler. Renamed to `st_name` (1.6.1).
2. **Per-IP-only brute-force limit.** It was ineffective against IP rotation (proven in the test environment, where each request used a new egress IP) and risky for shared CGNAT mobile IPs. Replaced with a **per-account failure limit** plus a generous per-IP limit (1.6.2).
3. Test hygiene: rate-limit counters were cleared once between test runs on the dev site.

## 4. Not done / decisions needed

- **Admin MFA:** not installed. Recommendation: the free **Two Factor** plugin (WordPress.org, maintained by WordPress contributors) for administrator accounts. **Owner approval needed** to add the dependency.
- **Staff seats / invitations:** the data model supports `owner` and `staff`, but there is no invite UI yet. It will come with the plan that grants seats.
- **Upgrade/downgrade, expiry and renewal states:** defined and built with checkout (Phase 07). The portal currently shows Free only.
- **Live email:** needs a sender address and domain authentication (SPF/DKIM/DMARC) in Phase 09; until then, test mode.
- Cosmetic: the legacy theme rule `h1{max-width:15ch}` wraps the portal greeting; to be fixed in the next theme/portal release.

## 5. Rollback

Install `releases/seller-tools-1.5.0.zip` (removes the portal and mail guard; user accounts and tables remain). Trash page #135 (`/app/`) and remove the "Sign in" menu item. Full fallback: R5.
