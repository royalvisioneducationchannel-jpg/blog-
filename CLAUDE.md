# Project instructions — Seller Toolkit SaaS

This repository holds the build pack for the **Seller Toolkit** WordPress SaaS.
Every session working on this project must follow it.

## Read first, every session

1. `docs/seller-toolkit/MASTER-PROMPT.md` — the full specification, rules and 14 phases (00–13). It is the source of truth.
2. `docs/seller-toolkit/PROJECT-STATUS.md` — completed phases, decisions, evidence, blockers and the exact next step.
3. `docs/seller-toolkit/START-HERE.md` — owner's usage guide (Roman Urdu).

## How we work

- Execute **one phase at a time**, and only the phase the owner asks for. Do not skip the audit, design review, payment verification or launch checks.
- Start with read-only inspection; preserve working tools, URLs, customer data and configuration. Destructive changes need explicit approval and a verified backup.
- Never ask for, print, log or commit credentials. Use official connection/authentication flows only.
- No real customer emails/SMS, live charges or paid API spend during development. Sandbox and mocked fixtures only.
- Never mark a simulated or unconfigured integration as working. Do not invent tools, APIs, prices, endorsements or claims.
- Update `PROJECT-STATUS.md` at the end of every phase (and when blocked).
- End each phase with the report format from the master prompt:
  1. Completed  2. Evidence  3. Needs owner input  4. Not yet live  5. Next phase
- Communicate progress in plain language; the owner is comfortable with Roman Urdu/English.

## Site safety (verified 2026-11-11)

- The WordPress connectors `Novalra` (royalvisiontraders.com, LIVE store) and `Royal_law` (royalvisionlaw.com) are **not** the dev site. Never use them for this project.
- The same Hostinger account hosts the live sites: always confirm the domain before any Hostinger write action.
- This repo is public: never commit credentials or account IDs, and don't commit plugin source until the repo is private.

## Known target (verify before use)

- Dev URL candidate: `https://lightyellow-alligator-410734.hostingersite.com/`
- Theme: `seller-toolkit` · Plugin: `seller-tools/seller-tools.php`
