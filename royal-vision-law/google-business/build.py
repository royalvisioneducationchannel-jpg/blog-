"""Google Business Profile graphics for Royal Vision Law Associate.

Writes one HTML page per graphic into ./html and renders them to PNG with render.mjs
(Playwright, 2x scale). Run: python3 build.py && node render.mjs
"""
import pathlib

HERE = pathlib.Path(__file__).resolve().parent
VEC = HERE.parent / 'vectors'
PHOTOS = HERE.parent / 'team-photos'
OUT = HERE / 'html'
OUT.mkdir(exist_ok=True)

svg = lambda name: (VEC / f'{name}.svg').read_text()
photo = lambda slug: (PHOTOS / f'{slug}.jpg').as_uri()

BASE = f"""
@font-face{{font-family:'Cormorant Garamond';font-style:normal;font-weight:300 700;src:url({(HERE/'fonts/cormorant-garamond.woff2').as_uri()}) format('woff2')}}
@font-face{{font-family:'Cormorant Garamond';font-style:italic;font-weight:300 700;src:url({(HERE/'fonts/cormorant-garamond-italic.woff2').as_uri()}) format('woff2')}}
@font-face{{font-family:'Manrope';font-style:normal;font-weight:200 800;src:url({(HERE/'fonts/manrope.woff2').as_uri()}) format('woff2')}}
:root{{--night:#0F2621;--deep:#0A1B16;--green:#1D4A3E;--gold:#B08D57;--gold2:#D4B77E;--ink:#7E5F26;--ivory:#F8F4EA;--serif:'Cormorant Garamond',Georgia,serif;--sans:'Manrope',system-ui,sans-serif}}
*{{box-sizing:border-box;margin:0;padding:0}}
html,body{{background:var(--deep)}}
body{{font-family:var(--sans);font-variant-numeric:lining-nums;-webkit-font-smoothing:antialiased}}
.frame{{position:relative;overflow:hidden;color:#fff}}
.dark{{background:radial-gradient(ellipse 70% 80% at 70% 45%,#245746 0%,#14372E 45%,#0A1B16 85%)}}
.lines::after{{content:"";position:absolute;inset:0;background:repeating-linear-gradient(115deg,rgba(212,183,126,.06) 0 1px,transparent 1px 22px);pointer-events:none}}
.eyebrow{{font-family:var(--sans);font-weight:700;font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:var(--gold2);display:flex;align-items:center;gap:12px}}
.eyebrow::before{{content:"";width:28px;height:1px;background:var(--gold)}}
.serif{{font-family:var(--serif);font-weight:600}}
.rule{{display:flex;align-items:center;gap:10px}}
.rule::before,.rule::after{{content:"";width:46px;height:1px;background:var(--gold)}}
.rule span{{width:20px;height:20px;background:url("data:image/svg+xml,{svg('scales').replace('#','%23').replace('"',"'")}") center/contain no-repeat}}
"""

def page(name, w, h, body, css=''):
    (OUT / f'{name}.html').write_text(
        f'<!doctype html><html><head><meta charset="utf-8"><style>{BASE}{css}</style></head>'
        f'<body><div class="frame" id="art" style="width:{w}px;height:{h}px">{body}</div></body></html>')

# 1. Logo: square, everything inside the circle Google crops to
page('logo', 540, 540, f"""
<div class="dark lines" style="position:absolute;inset:0"></div>
<div style="position:absolute;inset:34px;border-radius:50%;border:2px solid var(--gold);box-shadow:inset 0 0 0 9px rgba(10,27,22,.6),inset 0 0 0 10px rgba(212,183,126,.45)"></div>
<div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;z-index:1">
  <div style="width:120px;height:120px;margin-bottom:16px">{svg('scales').replace('stroke-width="1.6"','stroke-width="2.4"')}</div>
  <div class="serif" style="font-size:46px;line-height:1;letter-spacing:.04em;color:#fff">ROYAL VISION</div>
  <div style="width:200px;height:1px;background:var(--gold);margin:16px 0 14px"></div>
  <div style="font-weight:700;font-size:15px;letter-spacing:.42em;color:var(--gold2);padding-left:.42em">LAW ASSOCIATE</div>
  <div style="font-weight:600;font-size:10px;letter-spacing:.3em;color:rgba(255,255,255,.6);margin-top:14px;padding-left:.3em">EST. 2023 · FAISALABAD</div>
</div>""")

# 2. Cover photo: 16:9, text on the left, courthouse on the right
page('cover', 1024, 576, f"""
<div class="dark lines" style="position:absolute;inset:0"></div>
<div style="position:absolute;right:56px;bottom:0;width:430px;height:484px;filter:drop-shadow(0 30px 50px rgba(0,0,0,.5))">{svg('courthouse')}</div>
<div style="position:absolute;left:64px;top:0;bottom:0;width:520px;display:flex;flex-direction:column;justify-content:center;z-index:1">
  <div class="eyebrow">Advocates &amp; Legal Consultants · Faisalabad</div>
  <div class="serif" style="font-size:64px;line-height:1.02;margin:22px 0 14px">Royal Vision<br>Law Associate</div>
  <div style="font-family:var(--serif);font-style:italic;font-weight:500;font-size:30px;color:var(--gold2)">Clear advice. Considered action.</div>
  <div style="width:84px;height:2px;background:var(--gold);margin:28px 0 22px"></div>
  <div style="font-size:15px;line-height:1.9;color:#DCE5E0">Registered with the Punjab Bar Council · Reg. No. 13294/23<br>45 District Courts, Faisalabad · +92 334 5298703</div>
</div>""")

# 3. Founders: two arched portraits with name plaques
def arch(slug, name, role):
    return f"""<div style="display:flex;flex-direction:column;align-items:center;width:220px">
  <div style="width:196px;aspect-ratio:4/5;border-radius:50% 50% 4px 4px/40% 40% 4px 4px;overflow:hidden;background:url({photo(slug)}) center/cover;box-shadow:0 0 0 1px var(--gold2),0 0 0 7px rgba(10,27,22,.6),0 0 0 8px rgba(212,183,126,.45),0 30px 50px -24px rgba(0,0,0,.8)"></div>
  <div style="margin-top:-20px;position:relative;width:220px;padding:11px 8px 12px;text-align:center;background:linear-gradient(180deg,#132F28,#0B1D18);border:1px solid rgba(212,183,126,.55);border-top:2px solid var(--gold)">
    <div class="serif" style="font-size:17px;line-height:1.2;white-space:nowrap">{name}</div>
    <div style="font-weight:700;font-size:8.5px;letter-spacing:.2em;color:var(--gold2);margin-top:5px">{role}</div>
  </div></div>"""
page('photo-founders', 540, 540, f"""
<div class="dark lines" style="position:absolute;inset:0"></div>
<div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;padding-top:40px;z-index:1">
  <div class="eyebrow" style="justify-content:center">Royal Vision Law Associate</div>
  <div class="serif" style="font-size:40px;margin:12px 0 10px">Founding Advocates</div>
  <div class="rule"><span></span></div>
  <div style="display:flex;gap:22px;margin-top:34px">
    {arch('ch-abdul-nabi-qamar','Adv. Ch. Abdul Nabi Qamar','FOUNDING ADVOCATE')}
    {arch('imran-ali','Adv. Imran Ali','FOUNDING ADVOCATE')}
  </div>
</div>""")

# 4. Team grid: all seven advocates
team = [('ch-abdul-nabi-qamar','Ch. Abdul Nabi Qamar'),('imran-ali','Imran Ali'),('arshad-mehmood-warraich','Ch. Arshad Mehmood Warraich'),
        ('younis-amin','Younis Amin'),('shahid-amin','Shahid Amin'),('rana-tahir-mehmood','Rana Tahir Mehmood'),('ch-umar-shahzad-ashraf','Ch. Umar Shahzad Ashraf')]
def card(slug, name):
    return f"""<div style="width:104px;text-align:center">
  <div style="width:104px;aspect-ratio:4/5;background:url({photo(slug)}) center/cover;box-shadow:0 0 0 1px rgba(212,183,126,.7),0 16px 26px -16px rgba(0,0,0,.8)"></div>
  <div class="serif" style="font-size:13.5px;line-height:1.15;margin-top:7px;min-height:31px">{name}</div>
  <div style="font-weight:700;font-size:7px;letter-spacing:.2em;color:var(--gold2);margin-top:3px">ADVOCATE</div></div>"""
page('photo-team', 540, 540, f"""
<div class="dark lines" style="position:absolute;inset:0"></div>
<div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;padding-top:24px;z-index:1">
  <div class="eyebrow">Our People</div>
  <div class="serif" style="font-size:34px;margin:8px 0 6px">Our Advocates</div>
  <div class="rule"><span></span></div>
  <div style="display:flex;gap:14px;margin-top:16px">{''.join(card(*m) for m in team[:4])}</div>
  <div style="display:flex;gap:14px;margin-top:10px">{''.join(card(*m) for m in team[4:])}</div>
</div>""")

# 5. Practice areas: ivory, numbered
areas = [('Civil litigation','Suits, recovery claims and appeals'),('Criminal defence','From bail applications to trial'),('Family law','Divorce, custody, maintenance, guardianship'),
         ('Property &amp; land','Title checks, transfers, disputes'),('Corporate &amp; commercial','Contracts and company matters'),('Banking &amp; recovery','Recovery suits, cheque dishonour')]
items = ''.join(f"""<div style="padding:18px 18px 16px;background:#fff;border:1px solid #EAE1CF;border-top:2px solid var(--gold);position:relative">
  <div style="position:absolute;top:10px;right:14px;font-family:var(--serif);font-weight:600;font-size:34px;line-height:1;color:transparent;-webkit-text-stroke:1px rgba(176,141,87,.7)">{i+1:02d}</div>
  <div class="serif" style="font-size:21px;color:#14302A;padding-right:44px">{t}</div>
  <div style="font-size:11.5px;line-height:1.5;color:#4A5157;margin-top:6px">{d}</div></div>""" for i,(t,d) in enumerate(areas))
page('photo-practice-areas', 540, 540, f"""
<div style="position:absolute;inset:0;background:#F8F4EA"></div>
<div style="position:absolute;inset:0;padding:40px 40px 0;z-index:1;color:#14302A">
  <div class="eyebrow" style="color:var(--ink)">Practice Areas</div>
  <div class="serif" style="font-size:40px;margin:10px 0 8px">How we can help</div>
  <div class="rule"><span style="background-image:url(&quot;data:image/svg+xml,{svg('scales').replace('#D4B77E','#9A7742').replace('#','%23').replace(chr(34),chr(39))}&quot;)"></span></div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:22px">{items}</div>
</div>""")

# 6. Registration
page('photo-registration', 540, 540, f"""
<div class="dark lines" style="position:absolute;inset:0"></div>
<div style="position:absolute;right:-120px;bottom:-110px;width:330px;height:372px;opacity:.1">{svg('courthouse')}</div>
<div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:0 50px;z-index:1">
  <div style="width:150px;height:150px;border-radius:50%;border:2px solid var(--gold);box-shadow:inset 0 0 0 7px rgba(10,27,22,.6),inset 0 0 0 8px rgba(212,183,126,.45);display:flex;align-items:center;justify-content:center"><div style="width:70px;height:70px">{svg('scales')}</div></div>
  <div class="eyebrow" style="margin-top:28px">Registration</div>
  <div class="serif" style="font-size:40px;line-height:1.1;margin:12px 0 18px">Registered with the<br>Punjab Bar Council</div>
  <div style="display:flex;gap:0;border:1px solid rgba(212,183,126,.45)">
    <div style="padding:12px 20px;border-right:1px solid rgba(212,183,126,.45)"><div style="font-weight:700;font-size:9px;letter-spacing:.2em;color:var(--gold2)">REG. NO.</div><div class="serif" style="font-size:24px;margin-top:2px">13294/23</div></div>
    <div style="padding:12px 20px"><div style="font-weight:700;font-size:9px;letter-spacing:.2em;color:var(--gold2)">REGISTERED</div><div class="serif" style="font-size:24px;margin-top:2px">August 2023</div></div>
  </div>
  <div style="font-size:12.5px;line-height:1.7;color:#C9D5CF;margin-top:20px">Under Chapter VII of the Punjab Legal Practitioners<br>&amp; Bar Council Rules, 1974</div>
</div>""")

# 7. Contact / visit the chambers
row = lambda k, v: f"""<div style="padding:14px 0;border-bottom:1px solid rgba(255,255,255,.16)"><div style="font-weight:700;font-size:9.5px;letter-spacing:.22em;color:var(--gold2)">{k}</div><div class="serif" style="font-size:24px;margin-top:3px">{v}</div></div>"""
page('photo-contact', 540, 540, f"""
<div class="dark lines" style="position:absolute;inset:0"></div>
<div style="position:absolute;left:-50px;bottom:-40px;width:300px;height:300px;opacity:.07">{svg('scales')}</div>
<div style="position:absolute;inset:0;padding:48px 52px;z-index:1">
  <div class="eyebrow">Contact</div>
  <div class="serif" style="font-size:42px;line-height:1.08;margin:12px 0 22px">Visit our chambers</div>
  <div style="border-top:1px solid rgba(255,255,255,.16)">
    {row('CHAMBERS','45 District Courts, Faisalabad')}
    {row('TELEPHONE','+92 334 5298703')}
    {row('WEBSITE','royalvisionlaw.com')}
  </div>
  <div style="margin-top:26px;display:inline-block;padding:13px 24px;background:linear-gradient(135deg,#D2B27A,#B08D57 55%,#94733F);color:var(--night);font-weight:700;font-size:11px;letter-spacing:.16em">BOOK A CONSULTATION</div>
</div>""")
print('written:', ', '.join(sorted(p.stem for p in OUT.glob('*.html'))))
