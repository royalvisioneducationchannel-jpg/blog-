import sys, os, numpy as np, potrace
from PIL import Image, ImageFilter
A, OUT = sys.argv[1], sys.argv[2]
for name, mm in (("mark-gold.png", 96), ("word-gold.png", 92)):
    im = Image.open(os.path.join(A, name)).convert("RGBA"); im = im.crop(im.getbbox())
    a = im.getchannel("A").resize((im.width * 4, im.height * 4), Image.LANCZOS).filter(ImageFilter.GaussianBlur(2))
    bm = potrace.Bitmap(np.array(a) <= 127)
    path = bm.trace(turdsize=8, alphamax=1.0, opticurve=True, opttolerance=0.2)
    w, h = a.size; s = mm / w
    parts = []
    for c in path:
        st = c.start_point; d = [f"M{st.x*s:.3f},{st.y*s:.3f}"]
        for seg in c.segments:
            if seg.is_corner: d.append(f"L{seg.c.x*s:.3f},{seg.c.y*s:.3f}L{seg.end_point.x*s:.3f},{seg.end_point.y*s:.3f}")
            else: d.append(f"C{seg.c1.x*s:.3f},{seg.c1.y*s:.3f} {seg.c2.x*s:.3f},{seg.c2.y*s:.3f} {seg.end_point.x*s:.3f},{seg.end_point.y*s:.3f}")
        parts.append("".join(d) + "Z")
    svg = f'<svg xmlns="http://www.w3.org/2000/svg" width="{mm}mm" height="{h*s:.2f}mm" viewBox="0 0 {mm} {h*s:.3f}"><title>Lumira foil plate - {name[:4]} - {mm} mm wide</title><path fill="#000" fill-rule="evenodd" d="{"".join(parts)}"/></svg>'
    fn = os.path.join(OUT, f"Lumira-Foil-{'Mark' if 'mark' in name else 'Wordmark'}-{mm}mm.svg")
    open(fn, "w").write(svg); print(fn, len(path), f"{h*s:.1f}mm tall")
