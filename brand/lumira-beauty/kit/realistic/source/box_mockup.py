"""Lumira ritual kit box mockups at the print-file size (lid 243 x 178 x 67, base 240 x 175 x 65, insert 235 x 170 x 45)."""
import sys, os, math
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import scene as S
from scene import *

LW, LD, LH = mm(243), mm(178), mm(67)
BW, BD, BH = mm(240), mm(175), mm(65)
IL, ID, IH = mm(235), mm(170), mm(45)
FLOOR = mm(2)
ITOP = FLOOR + IH

def lamp(name, loc, target, power, size, color=(1, 0.97, 0.92)):
    o = S.light(name, loc, target, power, size, color); o.visible_camera = False; return o

def olive_mat(): return m_paper("box_olive", OLIVE, 0.72, sheen=0.35)

def closed_box():
    olive = olive_mat()
    box = cube("box", LW, LD, LH, (0, 0, LH / 2), bevel=mm(1.2), m=olive)
    seam = cube("seam", LW + mm(0.2), LD + mm(0.2), mm(0.5), (0, 0, mm(1.0)), m=m_plastic("seam", (0.02, 0.026, 0.014, 1), 0.8))
    bw, bx = mm(43), mm(60)
    cream = m_paper("sleeve", (0.94, 0.915, 0.85, 1), 0.85)
    sleeve = cube("sleeve", bw, LD + mm(1.2), LH + mm(1.2), (bx, 0, (LH + mm(1.2)) / 2 - mm(0.6)), bevel=mm(0.5), m=cream)
    band = plane("band_print", bw - mm(0.6), LD + mm(0.6), (bx, 0, LH + mm(0.62)), m=m_decal_print("band", os.path.join(TEX, "band.png")))
    mw = mm(96)
    mark = plane("lid_mark", mw, mw * 715 / 671, (mm(-34), 0, LH + mm(0.05)), m=m_decal_foil("lid_foil", os.path.join(S.AS, "mark-gold.png")))
    ww = mm(92)
    word = plane("front_word", ww, ww * 186 / 1202, (mm(-34), -LD / 2 - mm(0.05), LH * 0.52), rot=(math.radians(90), 0, 0), m=m_decal_foil("word_foil", os.path.join(S.AS, "word-gold.png")))
    return [box, seam, sleeve, band, mark, word]

def shell(name, L, D, H, t, m, up=True):
    """Open box built from a slab and four walls (robust, no booleans). up=True: open at the top."""
    zf = t / 2 if up else H - t / 2
    parts = [cube(name + "_slab", L, D, t, (0, 0, zf), bevel=mm(0.6), m=m)]
    parts.append(cube(name + "_wf", L, t, H, (0, -D / 2 + t / 2, H / 2), bevel=mm(0.6), m=m))
    parts.append(cube(name + "_wb", L, t, H, (0, D / 2 - t / 2, H / 2), bevel=mm(0.6), m=m))
    parts.append(cube(name + "_wl", t, D - 2 * t, H, (-L / 2 + t / 2, 0, H / 2), bevel=mm(0.4), m=m))
    parts.append(cube(name + "_wr", t, D - 2 * t, H, (L / 2 - t / 2, 0, H / 2), bevel=mm(0.4), m=m))
    return parts

def lid_upright(with_sleeve=False):
    """Lid shell, open side down, origin at its bottom rim."""
    olive = olive_mat()
    walls = shell("lid", LW, LD, LH, mm(2), olive, up=False)
    mw = mm(96)
    mark = plane("lid_mark", mw, mw * 715 / 671, (mm(-34), 0, LH + mm(0.05)), m=m_decal_foil("lid_foil", os.path.join(S.AS, "mark-gold.png")))
    ww = mm(92)
    word = plane("front_word", ww, ww * 186 / 1202, (mm(-34), -LD / 2 - mm(0.05), LH * 0.52), rot=(math.radians(90), 0, 0), m=m_decal_foil("word_foil", os.path.join(S.AS, "word-gold.png")))
    under = plane("lid_print", LW - mm(4.2), LD - mm(4.2), (0, 0, LH - mm(2.05)), rot=(math.pi, 0, 0), m=m_decal_print("lidin", os.path.join(TEX, "lidin.png"), rough=0.75))
    return walls + [mark, word, under]

def base_with_products():
    olive = olive_mat()
    base = shell("base", BW, BD, BH, FLOOR, olive, up=True)
    tray = cube("insert", IL, ID, IH, (0, 0, FLOOR + IH / 2), bevel=mm(0.8), m=m_paper("insert", (0.80, 0.76, 0.66, 1), 0.9))
    cuts = []
    def slot(name, x0, x1, y, r, depth):
        axis_z = ITOP - depth + r
        c = cyl(name, r, mm(x1 - x0), 0); c.rotation_euler = (0, math.radians(90), 0); c.location = (mm(x0), mm(y), axis_z)
        top = cube(name + "_top", mm(x1 - x0), 2 * r, mm(30), (mm((x0 + x1) / 2), mm(y), axis_z + mm(15)))
        cuts.extend([c, top]); return axis_z
    tube_z = slot("cut_tube", -95, 67, 42, mm(21), mm(25))
    cuts.append(cube("cut_crimp", mm(11), mm(64), mm(30), (mm(61.5), mm(42), ITOP - mm(12) + mm(15))))
    ser_z = slot("cut_ser", -105, 15, -45, mm(18), mm(22))
    cuts.append(cyl("cut_jar", mm(34), mm(60), ITOP - mm(40), loc=(mm(60), mm(-38))))
    cuts.append(cyl("notch1", mm(10), mm(60), ITOP - mm(30), loc=(mm(60), mm(-72))))
    cuts.append(cyl("notch2", mm(10), mm(60), ITOP - mm(20), loc=(mm(-45), mm(-63))))
    S.boolean(tray, cuts)
    objs = base + [tray]
    objs.append(S.place(tube(), (mm(-92.5), mm(42), tube_z), (-90, 0, -90)))
    objs.append(S.place(serum(), (mm(-102), mm(-45), ser_z), (-90, 0, -90)))
    objs.append(S.place(jar(), (mm(60), mm(-38), ITOP - mm(40)), (0, 0, -8)))
    return objs

def shot_box_hero():
    stage(); world()
    group(closed_box(), (0, 0, 0), rot_z=-22, name="box")
    tgt = (0, 0, mm(30))
    lamp("key", (-0.55, -0.5, 0.8), tgt, 18, 0.9)
    lamp("fill", (0.7, -0.45, 0.3), tgt, 5, 1.2)
    lamp("rim", (0.3, 0.6, 0.55), tgt, 12, 0.6, color=(1, 0.95, 0.88))
    camera((0.26, -0.64, 0.38), tgt, lens=70, fstop=8)

def shot_box_open():
    stage(); world()
    g = group(base_with_products(), (mm(-10), mm(-40), 0), rot_z=-4, name="base")
    lid = lid_upright()
    e = S.place(lid, (mm(20), mm(165), LH), (180, 0, 3))   # flipped: inside print faces up
    tgt = (0, mm(60), mm(30))
    lamp("key", (-0.5, -0.35, 0.95), tgt, 22, 1.0)
    lamp("fill", (0.7, -0.2, 0.5), tgt, 6, 1.2)
    lamp("rim", (0.1, 0.8, 0.7), tgt, 10, 0.8)
    camera((0.02, -0.30, 0.66), tgt, lens=58, fstop=11)

def shot_box_reveal():
    stage(); world()
    group(base_with_products(), (0, 0, 0), rot_z=-18, name="base")
    lid = lid_upright()
    e = S.place(lid, (mm(40), mm(115), mm(150)), (22, -4, -16))
    tgt = (0, mm(25), mm(80))
    lamp("key", (-0.55, -0.45, 0.9), tgt, 20, 1.0)
    lamp("fill", (0.7, -0.4, 0.35), tgt, 5, 1.2)
    lamp("rim", (0.25, 0.7, 0.7), tgt, 12, 0.7, color=(1, 0.95, 0.88))
    camera((0.22, -0.66, 0.5), tgt, lens=55, fstop=9)

SH = {"box_hero": shot_box_hero, "box_open": shot_box_open, "box_reveal": shot_box_reveal}
if __name__ == "__main__":
    name, out = sys.argv[1], sys.argv[2]
    w, h, spp = int(sys.argv[3]), int(sys.argv[4]), int(sys.argv[5])
    S.reset(); SH[name](); S.render(out, w, h, spp)
