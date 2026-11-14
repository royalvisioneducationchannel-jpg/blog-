"""Lumira Ritual Kit box: layered print PSDs (300 dpi) + dimension sheet.

Run: PYTHONPATH=<pylib> python3 build.py <assets_dir> <out_dir>
All sizes in mm. Board thickness 2 mm (rigid 2-piece box).
"""
import os, sys, math, subprocess
from PIL import Image, ImageDraw, ImageFont, ImageFilter, ImageChops

ASSETS, OUT = sys.argv[1], sys.argv[2]
os.makedirs(OUT, exist_ok=True)
TMP = os.path.join(OUT, "_layers"); os.makedirs(TMP, exist_ok=True)

DPI = 300
PX = DPI / 25.4
def p(mm): return int(round(mm * PX))

OLIVE = (90, 100, 64, 255)        # #5A6440 wrap
OLIVE_D = (68, 78, 54, 255)       # #444E36 ink
SAGE = (126, 140, 98, 255)        # #7E8C62 lid inside
CREAM = (244, 238, 226, 255)      # #F4EEE2 sleeve / insert
CREAM_T = (251, 248, 240, 255)    # #FBF8F0 text on sage
GOLD_T = (241, 227, 196, 255)     # #F1E3C4
CUT = (236, 0, 140, 255)          # magenta cut line
FOLD = (0, 160, 230, 255)         # cyan fold line
NOTE = (200, 30, 60, 255)

JOST = os.path.join(ASSETS, "Jost.ttf")
MARC = os.path.join(ASSETS, "Marcellus-Regular.ttf")
def font(path, mm_size, weight=None):
    f = ImageFont.truetype(path, p(mm_size))
    if weight:
        try: f.set_variation_by_axes([weight])
        except Exception: pass
    return f

def logo(name, width_mm, color=None):
    im = Image.open(os.path.join(ASSETS, name)).convert("RGBA")
    im = im.crop(im.getbbox())
    w = p(width_mm); h = round(im.height * w / im.width)
    im = im.resize((w, h), Image.LANCZOS)
    if color:
        a = im.getchannel("A")
        im = Image.new("RGBA", im.size, color); im.putalpha(a)
    return im

def tracked(draw, x, y, text, f, track_em, fill, anchor="l"):
    """Draw text with letter-spacing; anchor l/m/r on x, y = top."""
    tr = f.size * track_em
    widths = [draw.textlength(c, font=f) for c in text]
    total = sum(widths) + tr * (len(text) - 1)
    if anchor == "m": x -= total / 2
    elif anchor == "r": x -= total
    for c, w in zip(text, widths):
        draw.text((x, y), c, font=f, fill=fill)
        x += w + tr
    return total

def dashed(draw, a, b, color, w, dash=p(3), gap=p(2)):
    (x1, y1), (x2, y2) = a, b
    L = math.hypot(x2 - x1, y2 - y1); n = 0.0
    while n < L:
        e = min(n + dash, L)
        draw.line([(x1 + (x2 - x1) * n / L, y1 + (y2 - y1) * n / L), (x1 + (x2 - x1) * e / L, y1 + (y2 - y1) * e / L)], fill=color, width=w)
        n = e + gap

def blank(W, H): return Image.new("RGBA", (W, H), (0, 0, 0, 0))

def write_psd(name, size, layers, hidden=()):
    """layers: list of (label, RGBA image) bottom -> top. Composite = all visible layers."""
    comp = Image.new("RGBA", size, (255, 255, 255, 255))
    paths = []
    for i, (label, im) in enumerate(layers):
        if label not in hidden:
            comp.alpha_composite(im)
        fp = os.path.join(TMP, f"{name}_{i}.png"); im.save(fp); paths.append((label, fp))
    cp = os.path.join(TMP, f"{name}_comp.png"); comp.convert("RGB").save(cp)
    out = os.path.join(OUT, name + ".psd")
    cmd = ["convert", "-units", "PixelsPerInch", "-density", str(DPI), cp]
    for label, fp in paths:
        cmd += ["(", "-label", label, fp, ")"]
    cmd += [out]
    subprocess.run(cmd, check=True)
    if hidden:
        from psd_tools import PSDImage
        psd = PSDImage.open(out)
        for l in psd:
            if l.name in hidden: l.visible = False
        psd.save(out)
    comp.convert("RGB").save(os.path.join(OUT, name + "-preview.jpg"), quality=88, dpi=(DPI, DPI))
    return out

# ---------------------------------------------------------------- sizes
BOARD = 2.0
BASE = dict(L=240, D=175, H=65)           # outer
LID = dict(L=243, D=178, H=67)            # outer, full-telescope lid
TURN, FLAP, BLEED, MARGIN = 15, 15, 3, 25
INSERT = dict(L=235, D=170, H=45)

# ---------------------------------------------------------------- wrap (lid / base)
def wrap(name, B, title, art_fn=None, foil_fn=None):
    L, D, H = B["L"], B["D"], B["H"]
    Wmm = 2 * (MARGIN + TURN + H) + L; Hmm = 2 * (MARGIN + TURN + H) + D
    W, Hh = p(Wmm), p(Hmm)
    x0, y0 = MARGIN + TURN + H, MARGIN + TURN + H
    # panels (mm rects) and flaps (polygons)
    rects = {
        "top": (x0, y0, x0 + L, y0 + D),
        "front": (x0, y0 + D, x0 + L, y0 + D + H), "front_in": (x0, y0 + D + H, x0 + L, y0 + D + H + TURN),
        "back": (x0, y0 - H, x0 + L, y0), "back_in": (x0, y0 - H - TURN, x0 + L, y0 - H),
        "left": (x0 - H, y0, x0, y0 + D), "left_in": (x0 - H - TURN, y0, x0 - H, y0 + D),
        "right": (x0 + L, y0, x0 + L + H, y0 + D), "right_in": (x0 + L + H, y0, x0 + L + H + TURN, y0 + D),
    }
    t = 2  # flap taper
    flaps = [
        [(x0, y0 + D), (x0 - FLAP, y0 + D + t), (x0 - FLAP, y0 + D + H - t), (x0, y0 + D + H)],
        [(x0 + L, y0 + D), (x0 + L + FLAP, y0 + D + t), (x0 + L + FLAP, y0 + D + H - t), (x0 + L, y0 + D + H)],
        [(x0, y0), (x0 - FLAP, y0 - t), (x0 - FLAP, y0 - H + t), (x0, y0 - H)],
        [(x0 + L, y0), (x0 + L + FLAP, y0 - t), (x0 + L + FLAP, y0 - H + t), (x0 + L, y0 - H)],
    ]
    def shape_mask(grow_mm):
        m = Image.new("L", (W, Hh), 0); d = ImageDraw.Draw(m); g = grow_mm
        for (a, b, c, e) in rects.values():
            d.rectangle([p(a - g), p(b - g), p(c + g), p(e + g)], fill=255)
        for f in flaps:
            d.polygon([(p(x), p(y)) for x, y in f], fill=255)
        if g:
            m = m.filter(ImageFilter.MaxFilter(1))
        return m
    exact = shape_mask(0)
    bleed = Image.new("L", (W, Hh), 0); bd = ImageDraw.Draw(bleed)
    for (a, b, c, e) in rects.values():
        bd.rectangle([p(a - BLEED), p(b - BLEED), p(c + BLEED), p(e + BLEED)], fill=255)
    for f in flaps:
        cx = sum(x for x, _ in f) / 4; cy = sum(y for _, y in f) / 4
        bd.polygon([(p(x + (BLEED if x > cx else -BLEED)), p(y + (BLEED if y > cy else -BLEED))) for x, y in f], fill=255)
    # keep bleed out of the empty corner squares except where flaps sit
    bg = Image.new("RGBA", (W, Hh), OLIVE); bg.putalpha(bleed)
    layers = [("Olive wrap paper / print (with 3mm bleed)", bg)]
    if art_fn:
        art = blank(W, Hh); art_fn(art, x0, y0, L, D, H)
        layers.append(("Gold foil - PREVIEW (do not print)", art))
    if foil_fn:
        foil = blank(W, Hh); foil_fn(foil, x0, y0, L, D, H)
        layers.append(("FOIL + EMBOSS plate - spot GOLD 100K", foil))
    # fold lines
    fold = blank(W, Hh); fd = ImageDraw.Draw(fold); lw = p(0.3)
    for a, b in [((x0, y0), (x0 + L, y0)), ((x0, y0 + D), (x0 + L, y0 + D)), ((x0, y0), (x0, y0 + D)), ((x0 + L, y0), (x0 + L, y0 + D)),
                 ((x0, y0 + D + H), (x0 + L, y0 + D + H)), ((x0, y0 - H), (x0 + L, y0 - H)),
                 ((x0 - H, y0), (x0 - H, y0 + D)), ((x0 + L + H, y0), (x0 + L + H, y0 + D)),
                 ((x0, y0 + D), (x0, y0 + D + H)), ((x0 + L, y0 + D), (x0 + L, y0 + D + H)),
                 ((x0, y0 - H), (x0, y0)), ((x0 + L, y0 - H), (x0 + L, y0))]:
        dashed(fd, (p(a[0]), p(a[1])), (p(b[0]), p(b[1])), FOLD, lw)
    layers.append(("Dieline - FOLD (do not print)", fold))
    # cut line = outline of exact shape
    edge = ImageChops.subtract(exact.filter(ImageFilter.MaxFilter(5)), exact)
    cut = Image.new("RGBA", (W, Hh), CUT); cut.putalpha(edge)
    layers.append(("Dieline - CUT (do not print)", cut))
    # notes
    notes = blank(W, Hh); nd = ImageDraw.Draw(notes); f = font(JOST, 3.2, 500); fb = font(JOST, 4.2, 600)
    nd.text((p(8), p(6)), title, font=fb, fill=NOTE)
    nd.text((p(8), p(12)), f"Board outer {L} x {D} x {H} mm  |  2 mm greyboard  |  turn-in {TURN} mm  |  bleed {BLEED} mm  |  300 dpi  |  magenta = cut, cyan dashed = fold", font=f, fill=NOTE)
    def lab(r, text):
        a, b, c, e = r; nd.text((p((a + c) / 2), p((b + e) / 2)), text, font=f, fill=(255, 255, 255, 170), anchor="mm")
    fr, bk = rects["front"], rects["back"]
    nd.text((p(x0 + L / 2), p(fr[3] - 5)), f"FRONT  {L} x {H} mm", font=f, fill=(255, 255, 255, 170), anchor="mm")
    nd.text((p(x0 + L / 2), p(bk[1] + 5)), f"BACK  {L} x {H} mm", font=f, fill=(255, 255, 255, 170), anchor="mm")
    lab((x0 - H, y0, x0, y0 + D), f"LEFT {H}x{D}"); lab((x0 + L, y0, x0 + L + H, y0 + D), f"RIGHT {H}x{D}")
    for k in ("front_in", "back_in"): lab(rects[k], "turn-in 15")
    nd.text((p(x0 + L / 2), p(y0 + 8)), f"TOP  {L} x {D} mm", font=f, fill=(255, 255, 255, 170), anchor="mm")
    layers.append(("Notes & dimensions (do not print)", notes))
    return write_psd(name, (W, Hh), layers, hidden=("FOIL + EMBOSS plate - spot GOLD 100K",)), (W, Hh)

MARK_W, WORD_W, ART_X = 96, 92, -34   # from the approved 3D render
def lid_art(img, x0, y0, L, D, H, color=None):
    m = logo("mark-gold.png", MARK_W, color)
    cx, cy = x0 + L / 2 + ART_X, y0 + D / 2
    img.alpha_composite(m, (p(cx) - m.width // 2, p(cy) - m.height // 2))
    w = logo("word-gold.png", WORD_W, color)
    wy = y0 + D + (H - H * 0.52)          # word centre 52% up the front wall
    img.alpha_composite(w, (p(cx) - w.width // 2, p(wy) - w.height // 2))

lid_psd, lid_size = wrap("Lumira-Box-1-Lid-Wrap", LID, "LUMIRA RITUAL KIT - LID WRAP (outside)",
                         art_fn=lid_art, foil_fn=lambda *a: lid_art(*a, color=(0, 0, 0, 255)))
base_psd, base_size = wrap("Lumira-Box-2-Base-Wrap", BASE, "LUMIRA RITUAL KIT - BASE WRAP (outside, plain olive)")

# ---------------------------------------------------------------- sleeve
def sleeve():
    Wd = 43; seg = [("GLUE", 15), ("BOTTOM", LID["D"] + 1), ("FRONT", LID["H"] + 1), ("TOP", LID["D"] + 1), ("BACK", LID["H"] + 1)]
    Lmm = sum(s for _, s in seg)
    W, Hh = p(Lmm + 2 * MARGIN), p(Wd + 2 * MARGIN)
    x, y = MARGIN, MARGIN
    bg = blank(W, Hh); ImageDraw.Draw(bg).rectangle([p(x - BLEED), p(y - BLEED), p(x + Lmm + BLEED), p(y + Wd + BLEED)], fill=CREAM)
    txt = blank(W, Hh); td = ImageDraw.Draw(txt)
    top_x = x + 15 + seg[1][1] + seg[2][1]
    f = font(JOST, 3.8, 500)
    asc, desc = f.getmetrics()
    tracked(td, p(top_x + seg[3][1] / 2), p(y + Wd / 2) - (asc + desc) // 2 + p(0.3), "THE DAILY RITUAL  ·  CLEANSE · TREAT · HYDRATE", f, 0.42, OLIVE_D, anchor="m")
    fold = blank(W, Hh); fd = ImageDraw.Draw(fold); cx = x
    for _, s in seg[:-1]:
        cx += s; dashed(fd, (p(cx), p(y)), (p(cx), p(y + Wd)), FOLD, p(0.3))
    cut = blank(W, Hh); ImageDraw.Draw(cut).rectangle([p(x), p(y), p(x + Lmm), p(y + Wd)], outline=CUT, width=p(0.3))
    notes = blank(W, Hh); nd = ImageDraw.Draw(notes); fn = font(JOST, 3.2, 500)
    nd.text((p(x), p(6)), f"LUMIRA RITUAL KIT - SLEEVE / BELLY BAND  |  flat {Lmm} x {Wd} mm  |  cream textured 250-300 gsm, 1-colour olive  |  glue flap {15} mm  |  bleed 3 mm", font=fn, fill=NOTE)
    cx = x
    for n, s in seg:
        nd.text((p(cx + s / 2), p(y + Wd + 5)), f"{n} {s}", font=fn, fill=NOTE, anchor="mt"); cx += s
    nd.text((p(x), p(y + Wd + 13)), "Sleeve sits 60 mm right of the box centre (see render). Inner loop 2 x (179 + 68) = 494 mm slides over the closed box.", font=fn, fill=NOTE)
    layers = [("Cream paper / print (3mm bleed)", bg), ("Text - olive #444E36", txt), ("Dieline - FOLD (do not print)", fold), ("Dieline - CUT (do not print)", cut), ("Notes & dimensions (do not print)", notes)]
    return write_psd("Lumira-Box-3-Sleeve", (W, Hh), layers)
sleeve_psd = sleeve()

# ---------------------------------------------------------------- lid inside print
def lid_inside():
    Lmm, Dmm = LID["L"] - 2 * BOARD - 1, LID["D"] - 2 * BOARD - 1   # 238 x 173 trim
    W, Hh = p(Lmm + 2 * BLEED + 2 * 15), p(Dmm + 2 * BLEED + 2 * 15)
    ox, oy = 15 + BLEED, 15 + BLEED
    bg = blank(W, Hh); ImageDraw.Draw(bg).rectangle([p(ox - BLEED), p(oy - BLEED), p(ox + Lmm + BLEED), p(oy + Dmm + BLEED)], fill=SAGE)
    art = blank(W, Hh); ad = ImageDraw.Draw(art)
    m = logo("mark-cream.png", 23); art.alpha_composite(m, (p(ox + 17), p(oy + 19)))
    fh = font(MARC, 15)
    ad.text((p(ox + 17), p(oy + 50)), "Your daily", font=fh, fill=CREAM_T)
    ad.text((p(ox + 17), p(oy + 50 + 15.6)), "ritual", font=fh, fill=CREAM_T)
    fl = font(JOST, 4.6, 400); fb = font(JOST, 4.6, 700)
    for i, (n, t) in enumerate([("01", "CLEANSE — FACE WASH"), ("02", "TREAT — FACE SERUM"), ("03", "HYDRATE — MOISTURIZER")]):
        yy = p(oy + 92 + i * 9.7)
        w = tracked(ad, p(ox + 17.5), yy, n, fb, 0.14, GOLD_T)
        tracked(ad, p(ox + 17.5) + w + p(3), yy, t, fl, 0.14, CREAM_T)
    tracked(ad, p(ox + 17.5), p(oy + 150), "BY ROYAL VISION", font(JOST, 3.2, 400), 0.4, (223, 227, 210, 255))
    card = blank(W, Hh); cd = ImageDraw.Draw(card)
    cx0, cy0 = ox + Lmm - 105 - 22, oy + (Dmm - 148) / 2
    dashed(cd, (p(cx0), p(cy0)), (p(cx0 + 105), p(cy0)), CREAM_T, p(0.3)); dashed(cd, (p(cx0), p(cy0 + 148)), (p(cx0 + 105), p(cy0 + 148)), CREAM_T, p(0.3))
    dashed(cd, (p(cx0), p(cy0)), (p(cx0), p(cy0 + 148)), CREAM_T, p(0.3)); dashed(cd, (p(cx0 + 105), p(cy0)), (p(cx0 + 105), p(cy0 + 148)), CREAM_T, p(0.3))
    cd.text((p(cx0 + 52.5), p(cy0 + 74)), "A6 ritual card\n105 x 148\n(corner slits / ribbon)", font=font(JOST, 3.4, 500), fill=CREAM_T, anchor="mm", align="center")
    cut = blank(W, Hh); ImageDraw.Draw(cut).rectangle([p(ox), p(oy), p(ox + Lmm), p(oy + Dmm)], outline=CUT, width=p(0.3))
    notes = blank(W, Hh); nd = ImageDraw.Draw(notes)
    nd.text((p(ox), p(4)), f"LID INSIDE PRINT  |  trim {Lmm} x {Dmm} mm  |  bleed 3 mm  |  glued to inner lid top", font=font(JOST, 3.0, 500), fill=NOTE)
    layers = [("Sage background (3mm bleed)", bg), ("Artwork - cream & gold text", art), ("Card position guide (do not print)", card), ("Dieline - CUT / trim (do not print)", cut), ("Notes (do not print)", notes)]
    return write_psd("Lumira-Box-4-Lid-Inside-Print", (W, Hh), layers)
lidin_psd = lid_inside()

# ---------------------------------------------------------------- insert (top view, 1:1)
CAV = {  # insert coordinates: origin centre, +x right, +y towards the BACK; mm
    "tube": dict(x0=-95, x1=67, y=42, w=42, depth=25, crimp=(56, 67, 64, 12)),
    "serum": dict(x0=-105, x1=15, y=-45, w=36, depth=22),
    "jar": dict(cx=60, cy=-38, d=68, depth=40),
}
def insert():
    L, D = INSERT["L"], INSERT["D"]
    W, Hh = p(L + 2 * 30), p(D + 2 * 30)
    def X(x): return p(30 + L / 2 + x)
    def Y(y): return p(30 + D / 2 - y)
    foam = blank(W, Hh); ImageDraw.Draw(foam).rounded_rectangle([X(-L / 2), Y(D / 2), X(L / 2), Y(-D / 2)], radius=p(3), fill=CREAM)
    cav = blank(W, Hh); cd = ImageDraw.Draw(cav); fillc = (205, 196, 176, 255)
    t = CAV["tube"]; r = t["w"] / 2
    cd.rounded_rectangle([X(t["x0"]), Y(t["y"] + r), X(t["x1"]), Y(t["y"] - r)], radius=p(r), fill=fillc)
    a, b, wdt, _ = t["crimp"]; cd.rectangle([X(a), Y(t["y"] + wdt / 2), X(b), Y(t["y"] - wdt / 2)], fill=fillc)
    s = CAV["serum"]; r = s["w"] / 2
    cd.rounded_rectangle([X(s["x0"]), Y(s["y"] + r), X(s["x1"]), Y(s["y"] - r)], radius=p(r), fill=fillc)
    j = CAV["jar"]; r = j["d"] / 2
    cd.ellipse([X(j["cx"] - r), Y(j["cy"] + r), X(j["cx"] + r), Y(j["cy"] - r)], fill=fillc)
    for (fx, fy) in [(j["cx"], j["cy"] - r), (-45, s["y"] - s["w"] / 2)]:   # finger notches
        cd.ellipse([X(fx - 10), Y(fy + 10), X(fx + 10), Y(fy - 10)], fill=fillc)
    lab = blank(W, Hh); ld = ImageDraw.Draw(lab); f = font(JOST, 3.4, 600); fs = font(JOST, 2.8, 400)
    ld.text((X((t["x0"] + t["x1"]) / 2), Y(t["y"])), f"FACE WASH TUBE 100 ml\ncavity {t['x1'] - t['x0']} x {t['w']}, depth {t['depth']}  |  crimp slot 64 wide", font=f, fill=OLIVE_D, anchor="mm", align="center")
    ld.text((X((s["x0"] + s["x1"]) / 2), Y(s["y"])), f"SERUM 30 ml\ncavity {s['x1'] - s['x0']} x {s['w']}, depth {s['depth']}", font=f, fill=OLIVE_D, anchor="mm", align="center")
    ld.text((X(j["cx"]), Y(j["cy"])), f"MOISTURIZER\n50 g jar\nØ{j['d']}, depth {j['depth']}", font=f, fill=OLIVE_D, anchor="mm", align="center")
    ld.text((X(-L / 2), Y(D / 2) - p(16)), f"INSERT - top view 1:1  |  {L} x {D} x {INSERT['H']} mm  |  EVA foam (paper-wrapped) or moulded pulp  |  finger notches Ø20", font=font(JOST, 3.2, 600), fill=NOTE)
    ld.text((X(-L / 2), Y(D / 2) - p(10)), "FRONT of box = bottom edge. Cavity sizes include ~1 mm clearance; confirm with real product samples before cutting.", font=fs, fill=NOTE)
    cut = blank(W, Hh); ImageDraw.Draw(cut).rounded_rectangle([X(-L / 2), Y(D / 2), X(L / 2), Y(-D / 2)], radius=p(3), outline=CUT, width=p(0.3))
    layers = [("Foam block", foam), ("Cavities (cut-outs)", cav), ("Labels & sizes", lab), ("Outline (do not print)", cut)]
    return write_psd("Lumira-Box-5-Insert-Layout", (W, Hh), layers)
insert_psd = insert()
print("done")
