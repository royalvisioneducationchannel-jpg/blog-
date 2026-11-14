import sys, numpy as np, potrace
from PIL import Image, ImageFilter
src = sys.argv[1]
a = np.array(Image.open(src).convert("RGB")).astype(float)
a = a[:1420]  # drop divider line + "BY ROYAL VISION"
bg = np.array([254, 253, 249.])
inks = {"olive": np.array([95, 100, 71.]), "gold": np.array([178, 152, 118.])}
D = bg - a
best = None
for k, ink in inks.items():
    v = bg - ink
    t = (D @ v) / (v @ v)
    r = np.linalg.norm(D - t[..., None] * v, axis=2)
    if best is None: best = {"t": {k: t}, "r": {k: r}}
    else: best["t"][k] = t; best["r"][k] = r
gold = best["r"]["gold"] < best["r"]["olive"]
alpha = np.clip(np.where(gold, best["t"]["gold"], best["t"]["olive"]) * 1.35, 0, 1)
# gold drop lives only in the mark region; keep text olive
gold &= (np.arange(a.shape[0])[:, None] < 1100)
def trace(mask_alpha, scale=3):
    im = Image.fromarray((mask_alpha * 255).astype(np.uint8))
    im = im.resize((im.width * scale, im.height * scale), Image.LANCZOS).filter(ImageFilter.GaussianBlur(1.5))
    bm = potrace.Bitmap(np.array(im) <= 127)
    path = bm.trace(turdsize=30, alphamax=1.0, opticurve=True, opttolerance=0.2)
    s = 1 / scale; parts = []
    for c in path:
        st = c.start_point; d = [f"M{st.x*s:.2f},{st.y*s:.2f}"]
        for seg in c.segments:
            if seg.is_corner: d.append(f"L{seg.c.x*s:.2f},{seg.c.y*s:.2f}L{seg.end_point.x*s:.2f},{seg.end_point.y*s:.2f}")
            else: d.append(f"C{seg.c1.x*s:.2f},{seg.c1.y*s:.2f} {seg.c2.x*s:.2f},{seg.c2.y*s:.2f} {seg.end_point.x*s:.2f},{seg.end_point.y*s:.2f}")
        parts.append("".join(d) + "Z")
    return "".join(parts)
H = a.shape[0]
rows = np.arange(H)[:, None]
mark_rows = rows < 1100
d_mark_main = trace(alpha * (~gold) * mark_rows)
d_mark_gold = trace(alpha * gold)
d_word = trace(alpha * (rows >= 1100) * (rows < 1310))
d_sub = trace(alpha * (rows >= 1340))
import json
json.dump({"mark": d_mark_main, "drop": d_mark_gold, "word": d_word, "sub": d_sub}, open("paths.json", "w"))
print({k: len(v) for k, v in json.load(open("paths.json")).items()})
