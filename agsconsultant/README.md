# AGS Consultant website

Static site for https://agsconsultant.com.pk (HTML, CSS, vanilla JS, one PHP form handler).

## Structure
- `src/pages/` – page content (each file starts with a `<!--meta {...} -->` block: title, description, nav key)
- `src/partials/` – shared header, footer and SVG icon sprite
- `src/static/` – CSS, JS, images, PHP form handlers, robots.txt (copied as-is)
- `build.py` – assembles pages into `public/` (Python 3, no dependencies)
- `public/` – build output; upload its contents to `public_html`

## Build
```bash
python3 build.py
```

## Forms
`inquiry.php` handles the homepage and Apply Online forms (honeypot + timing spam check,
header-injection safe, JSON for JS submits, redirect to `thank-you.html` otherwise).
`mail-inquiry.php` and `mail.php` remain for old links.

## Deployment (live since 2026-11-02)
- `public/` contents are uploaded to `public_html` on the cPanel account.
- The previous site's pages are backed up (not web-accessible) in
  `/home/agsconsultantcom/site-backup-2026-11-02/`. Old images remain in `public_html/assets/images/`.
- New assets live under `public_html/assets/ags/` so they never overwrite the old theme files.
- The server runs Imunify360: upload slowly (a few seconds between files) or it blocks the client IP.
