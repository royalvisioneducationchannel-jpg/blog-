import json, sys
from PIL import Image, ImageDraw, ImageFilter
import numpy as np
P = json.loads(sys.argv[1])
base = Image.open('podium.png').convert('RGBA'); W, H = base.size
SRC_PX_PER_MM = 291 / 62.0
prods = {"mo": "p2.png", "fw": "p0.png", "se": "p1.png"}
for k in ("mo", "fw", "se"):
    p = Image.open(prods[k]).convert('RGBA')
    s = P[k]["px_per_mm"] / SRC_PX_PER_MM
    p = p.resize((round(p.width * s), round(p.height * s)), Image.LANCZOS)
    a = np.asarray(p).astype(float); a[..., :3] *= (0.98, 1.0, 0.96); p = Image.fromarray(a.clip(0, 255).astype(np.uint8))
    cx, by = P[k]["x"], P[k]["y"] + 4
    # cast shadow (key light from front-left -> falls back-right)
    sh = Image.new('RGBA', (W, H), (0, 0, 0, 0)); d = ImageDraw.Draw(sh)
    w = p.width
    d.ellipse((cx - w * 0.35, by - w * 0.28, cx + w * 0.95, by + w * 0.02), fill=(20, 28, 12, 70))
    sh = sh.filter(ImageFilter.GaussianBlur(w * 0.12)); base.alpha_composite(sh)
    # contact shadow
    cs = Image.new('RGBA', (W, H), (0, 0, 0, 0)); d = ImageDraw.Draw(cs)
    d.ellipse((cx - w * 0.52, by - w * 0.09, cx + w * 0.52, by + w * 0.07), fill=(15, 20, 8, 150))
    cs = cs.filter(ImageFilter.GaussianBlur(w * 0.04)); base.alpha_composite(cs)
    base.alpha_composite(p, (round(cx - p.width / 2), round(by - p.height)))
base.save('stage_hd.png')
bb = base.getchannel('A').point(lambda v: 255 if v > 30 else 0).getbbox(); print(base.size, bb)
chk = Image.new('RGBA', base.size, (95, 119, 68, 255)); chk.alpha_composite(base); chk.convert('RGB').resize((W // 2, H // 2)).save('stage_hd_chk.png')
