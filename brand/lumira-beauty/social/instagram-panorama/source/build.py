import json, sys
from PIL import Image
tops = json.loads(sys.argv[1]); P = json.load(open('../../logo-green/paths.json'))
st = Image.open('stage_crop.png'); ox, oy = json.loads(sys.argv[2])  # crop offset in stage px
SW = 1040; s = SW / st.width; SH = st.height * s
SL = 1620 - SW / 2; ST = 1440 - SH + 30
labels = ""
for k, txt in (("fw", "01 · CLEANSE"), ("se", "02 · TREAT"), ("mo", "03 · HYDRATE")):
    cx, ty = tops[k]; x = SL + (cx - ox) * s; y = ST + (ty - oy) * s - 70
    labels += f'<div class="step abs" style="left:{x-180:.0f}px;top:{y+20:.0f}px">{txt}</div>'
leaf = '<path d="M0 75 C 50 5 150 0 220 75 C 150 150 50 145 0 75 Z" fill="#E8C97E"/>'
html = f'''<!doctype html><html><head><meta charset="utf-8"><style>
@font-face{{font-family:Jost;src:url(Jost.ttf)}} @font-face{{font-family:Outfit;src:url(Outfit-Bold.ttf);font-weight:700}} @font-face{{font-family:BigS;src:url(BigShoulders-Bold.ttf)}}
html,body{{margin:0}} .abs{{position:absolute}}
#c{{position:relative;width:3240px;height:1440px;overflow:hidden;font-family:Jost;color:#fff;
 background:radial-gradient(760px 640px at 560px 760px,rgba(246,240,214,.5),rgba(246,240,214,0) 70%),
 radial-gradient(620px 560px at 1620px 820px,rgba(247,241,220,.42),rgba(247,241,220,0) 72%),
 linear-gradient(100deg,#A9BE8A 0%,#8CA36B 30%,#748B55 55%,#5E7443 80%,#52673A 100%)}}
.step{{width:360px;white-space:nowrap;text-align:center;font-weight:600;font-size:28px;letter-spacing:.22em;color:#F3DFA8;text-shadow:0 2px 8px rgba(30,40,20,.35)}}
.lab{{font-weight:600;letter-spacing:.34em;font-size:32px;color:#E8CC8A}}
</style></head><body><div id="c">
<svg class="abs" width="3240" height="1440" style="left:0;top:0">
 <defs><path id="arc" d="M 148 660 A 400 400 0 0 1 948 660"/><path id="arcT" d="M 92 660 A 456 456 0 0 1 1004 660"/>
 <g id="leaf"><path d="M0 0 C 50 -70 150 -75 220 0 C 150 75 50 70 0 0 Z"/><path d="M0 0 L 215 0" stroke-width="4" fill="none"/></g></defs>
 <g fill="#fff" stroke="#fff" opacity=".10">
  <use href="#leaf" transform="translate(-60 1350) rotate(-40) scale(2.4)"/>
  <use href="#leaf" transform="translate(900 120) rotate(18) scale(1.9)"/>
  <use href="#leaf" transform="translate(1980 1180) rotate(-28) scale(2.2)"/>
  <use href="#leaf" transform="translate(2070 140) rotate(25) scale(1.4)"/>
  <use href="#leaf" transform="translate(3300 1150) rotate(200) scale(2.6)"/>
  <use href="#leaf" transform="translate(2950 -60) rotate(70) scale(2)"/>
 </g>
 <g fill="#F7EBC8" opacity=".55"><circle cx="90" cy="980" r="7"/><circle cx="1010" cy="1060" r="5"/><circle cx="1180" cy="620" r="6"/><circle cx="2080" cy="520" r="5"/><circle cx="3120" cy="420" r="6"/><circle cx="2300" cy="1300" r="5"/></g>
 <path d="M 948 660 C 962 930 1050 1180 1300 1290 S 1900 1395 2250 1330 S 2900 1270 3300 1290" fill="none" stroke="#E8C97E" stroke-width="34" stroke-linecap="round"/>
 <path d="M 1010 1340 C 1300 1440 1800 1440 2200 1360 S 2900 1350 3300 1370" fill="none" stroke="#F3E2B4" stroke-width="6" opacity=".7"/>
 <use href="#arc" fill="none" stroke="#E8C97E" stroke-width="34" stroke-linecap="round"/>
 <text font-family="BigS" font-size="86" letter-spacing="6" fill="#fff" style="paint-order:stroke" stroke="rgba(40,55,25,.25)" stroke-width="6">
  <textPath href="#arcT" startOffset="50%" text-anchor="middle">3 STEPS TO YOUR DAILY GLOW</textPath></text>
 <text x="230" y="760" font-family="BigS" font-size="64" fill="#fff" text-anchor="middle">AM</text>
 <text x="866" y="760" font-family="BigS" font-size="64" fill="#fff" text-anchor="middle" style="paint-order:stroke" stroke="rgba(40,55,25,.25)" stroke-width="4">PM</text>
</svg>
<img class="abs" src="model_final.png" style="left:120px;top:322px;height:1180px">
<div class="abs" style="left:1080px;top:120px;width:1080px;text-align:center">
 <div class="lab">THE LUMIRA RITUAL</div>
 <div style="font-family:Outfit;font-weight:700;font-size:76px;margin-top:18px;line-height:1.05">Three Steps.<br>One Glow.</div>
</div>
<img class="abs" src="stage_crop.png" style="left:{SL:.0f}px;top:{ST:.0f}px;width:{SW}px">
{labels}
<div class="abs" style="left:2290px;top:250px;width:860px">
 <svg width="150" height="160" viewBox="660 380 700 720"><path fill-rule="evenodd" fill="#fff" d="{P['mark']}"/><path fill-rule="evenodd" fill="#E8C97E" d="{P['drop']}"/></svg>
 <div class="lab" style="margin-top:34px">LUMIRA SKINCARE</div>
 <div style="font-weight:300;font-size:112px;line-height:1;margin-top:26px">Your Daily</div>
 <div style="font-family:Outfit;font-weight:700;font-size:136px;line-height:1.02">Glow Ritual</div>
 <div style="display:inline-block;margin-top:44px;background:#E8C97E;color:#2E3A22;font-family:Outfit;font-weight:700;font-size:52px;letter-spacing:.06em;padding:20px 38px 18px;border-radius:6px;box-shadow:0 12px 30px rgba(0,0,0,.2)">LAUNCHING SOON</div>
 <div style="display:flex;flex-direction:column;gap:26px;margin-top:52px;font-weight:600;font-size:38px;letter-spacing:.02em">
  <div style="display:flex;align-items:center;gap:20px"><svg width="44" height="30" viewBox="0 0 220 150">{leaf}</svg>3-Step Kit: Cleanse · Treat · Hydrate</div>
  <div style="display:flex;align-items:center;gap:20px"><svg width="44" height="30" viewBox="0 0 220 150">{leaf}</svg>Cash on Delivery All Over Pakistan</div>
  <div style="display:flex;align-items:center;gap:20px"><svg width="44" height="30" viewBox="0 0 220 150">{leaf}</svg>Order via DM &amp; WhatsApp</div>
 </div>
</div>
</div></body></html>'''
open('ig.html', 'w').write(html); print('ok', SL, ST, SH)
