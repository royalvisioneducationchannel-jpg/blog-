#!/usr/bin/env python3
"""Build the AGS Consultant static site.

Pages live in src/pages/*.html. Each starts with a meta comment:
    <!--meta {"title": "...", "description": "...", "nav": "home"} -->
The shared header, footer and icon sprite in src/partials are wrapped around
each page, and src/static is copied as-is. Output goes to public/, which is
uploaded to public_html. Python 3 standard library only.
"""
import hashlib
import html
import json
import re
import shutil
from pathlib import Path

ROOT = Path(__file__).resolve().parent
SRC = ROOT / "src"
OUT = ROOT / "public"
SITE_URL = "https://agsconsultant.com.pk/"

NAV_KEYS = ["home", "about", "services", "faqs", "contact",
            "ielts", "languagecert", "duolingo", "spoken", "german"]
LANGUAGE_KEYS = {"ielts", "languagecert", "duolingo", "spoken", "german"}

HEAD = """<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{title}</title>
<meta name="description" content="{description}">
<link rel="canonical" href="{canonical}">
<meta name="theme-color" content="#021d68">
<meta property="og:type" content="website">
<meta property="og:site_name" content="AGS Consultant">
<meta property="og:title" content="{title}">
<meta property="og:description" content="{description}">
<meta property="og:url" content="{canonical}">
<meta property="og:image" content="{site}assets/ags/img/{og_image}">
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" type="image/png" href="assets/ags/img/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Manrope:wght@600;700;800&display=swap">
<link rel="stylesheet" href="assets/ags/css/site.css?v={css_v}">
{extra_head}</head>
<body>
"""

TAIL = """<script src="assets/ags/js/site.js?v={js_v}" defer></script>
</body>
</html>
"""


def file_hash(path):
    return hashlib.md5(path.read_bytes()).hexdigest()[:8]


def render_header(template, nav):
    out = template
    for key in NAV_KEYS:
        out = out.replace("{{cur:%s}}" % key, ' aria-current="page"' if key == nav else "")
    out = out.replace("{{active:language}}", " is-active" if nav in LANGUAGE_KEYS else "")
    return out


def build():
    if OUT.exists():
        shutil.rmtree(OUT)
    shutil.copytree(SRC / "static", OUT)

    css_v = file_hash(SRC / "static/assets/ags/css/site.css")
    js_v = file_hash(SRC / "static/assets/ags/js/site.js")
    header_t = (SRC / "partials/header.html").read_text(encoding="utf-8")
    footer = (SRC / "partials/footer.html").read_text(encoding="utf-8")
    icons = (SRC / "partials/icons.svg").read_text(encoding="utf-8")

    built = []
    for page in sorted((SRC / "pages").glob("*.html")):
        raw = page.read_text(encoding="utf-8")
        m = re.match(r"\s*<!--meta\s+(\{.*?\})\s*-->\s*", raw, re.S)
        if not m:
            raise SystemExit(f"{page.name}: missing meta comment")
        meta = json.loads(m.group(1))
        body = raw[m.end():]
        canonical = SITE_URL if page.name == "index.html" else SITE_URL + page.name
        head = HEAD.format(
            title=html.escape(meta["title"]),
            description=html.escape(meta["description"]),
            canonical=canonical,
            site=SITE_URL,
            og_image=meta.get("og_image", "hero-counselling-session.webp"),
            css_v=css_v,
            extra_head=meta.get("extra_head", ""),
        )
        doc = (head + icons + render_header(header_t, meta.get("nav", ""))
               + '<main id="main">\n' + body.rstrip() + "\n</main>\n"
               + footer + TAIL.format(js_v=js_v))
        (OUT / page.name).write_text(doc, encoding="utf-8")
        if not meta.get("noindex"):
            built.append(canonical)

    urls = "\n".join(f"  <url><loc>{u}</loc></url>" for u in built)
    (OUT / "sitemap.xml").write_text(
        '<?xml version="1.0" encoding="UTF-8"?>\n'
        '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n'
        f"{urls}\n</urlset>\n", encoding="utf-8")
    print(f"Built {len(list((SRC / 'pages').glob('*.html')))} pages -> {OUT}")


if __name__ == "__main__":
    build()
