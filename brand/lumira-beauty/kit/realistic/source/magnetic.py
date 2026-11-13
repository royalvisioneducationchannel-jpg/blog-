"""Lumira magnetic-closure rigid box (book-style lid, front flap), dark olive foam insert."""
import sys, os, math
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import scene as S
from scene import *
import box_mockup as B

BW, BD, BH = mm(240), mm(175), mm(65)
T = mm(2.5)                      # lid board
LW, LD = mm(244), mm(178)        # lid top panel
FH = mm(45)                      # front flap height
HINGE = (0, BD / 2, BH)

def foam_mat():
    m, nt, out = mat("foam")
    b = bsdf(nt, (0.042, 0.054, 0.032, 1), 0.92, sheen=0.25, spec=0.15)
    paper_bump(nt, b, scale=2500, strength=0.15)
    nt.links.new(b.outputs[0], out.inputs[0]); return m

def insert_with_products():
    tray = cube("insert", B.IL, B.ID, B.IH, (0, 0, B.FLOOR + B.IH / 2), m=foam_mat())
    for p in tray.data.polygons: p.use_smooth = False
    cuts, ITOP = [], B.ITOP
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
    objs = [tray]
    objs.append(S.place(tube(), (mm(-92.5), mm(42), tube_z), (-90, 0, -90)))
    objs.append(S.place(serum(), (mm(-102), mm(-45), ser_z), (-90, 0, -90)))
    objs.append(S.place(jar(), (mm(60), mm(-38), ITOP - mm(40)), (0, 0, -8)))
    # cream satin pull ribbon tucked under the insert's front edge
    rib = m_paper("ribbon", (0.93, 0.89, 0.80, 1), 0.35, sheen=0.6)
    objs.append(cube("ribbon_v", mm(14), mm(0.6), mm(18), (mm(-60), -B.ID / 2 - mm(0.4), ITOP - mm(9)), m=rib))
    objs.append(cube("ribbon_h", mm(14), mm(10), mm(0.6), (mm(-60), -B.ID / 2 + mm(4.6), ITOP + mm(0.3)), m=rib))
    return objs

def magnetic_box(open_deg=0.0):
    olive = B.olive_mat()
    root = bpy.data.objects.new("mag", None); bpy.context.scene.collection.objects.link(root)
    parts = B.shell("base", BW, BD, BH, B.FLOOR, olive, up=True) + insert_with_products()
    spine = cube("spine", LW, T, BH + T, (0, BD / 2 + T / 2, (BH + T) / 2), bevel=mm(0.5), m=olive)
    parts.append(spine)
    for o in parts:
        if o.parent is None: o.parent = root
    hinge = bpy.data.objects.new("hinge", None); bpy.context.scene.collection.objects.link(hinge)
    hinge.parent = root; hinge.location = HINGE
    top = cube("lid_top", LW, LD + T, T, (0, -(LD + T) / 2 + T, T / 2), bevel=mm(0.6), m=olive)
    flap = cube("lid_flap", LW, T, FH + T, (0, -LD - T / 2 + T, T - (FH + T) / 2), bevel=mm(0.6), m=olive)
    mark = plane("lid_mark", mm(72), mm(72) * 715 / 671, (0, -LD / 2 + mm(6), T + mm(0.05)), m=m_decal_foil("lid_foil", os.path.join(S.AS, "mark-gold.png")))
    word = plane("flap_word", mm(84), mm(84) * 186 / 1202, (0, -LD - mm(0.06) + T - T, -FH / 2 + mm(4)), rot=(math.radians(90), 0, 0), m=m_decal_foil("word_foil", os.path.join(S.AS, "word-gold.png")))
    under = plane("lid_print", LW - mm(8), LD - mm(8), (0, -LD / 2 + T, -mm(0.06)), rot=(math.pi, 0, 0), m=m_decal_print("lidin", os.path.join(TEX, "lidin.png"), rough=0.75))
    for o in (top, flap, mark, word, under): o.parent = hinge
    hinge.rotation_euler = (math.radians(-open_deg), 0, 0)
    return root

def shot_mag_closed():
    stage(); world()
    r = magnetic_box(0); r.rotation_euler = (0, 0, math.radians(-24))
    tgt = (0, 0, mm(32))
    B.lamp("key", (-0.55, -0.5, 0.8), tgt, 18, 0.9)
    B.lamp("fill", (0.7, -0.45, 0.3), tgt, 5, 1.2)
    B.lamp("rim", (0.3, 0.6, 0.55), tgt, 12, 0.6, color=(1, 0.95, 0.88))
    camera((0.26, -0.64, 0.40), tgt, lens=70, fstop=8)

def shot_mag_open():
    stage(); world()
    r = magnetic_box(108); r.rotation_euler = (0, 0, math.radians(-16))
    tgt = (0, mm(15), mm(80))
    B.lamp("key", (-0.5, -0.5, 0.95), tgt, 22, 1.0)
    B.lamp("fill", (0.7, -0.45, 0.4), tgt, 6, 1.2)
    B.lamp("rim", (0.25, 0.7, 0.7), tgt, 10, 0.7, color=(1, 0.95, 0.88))
    camera((0.18, -0.52, 0.66), tgt, lens=56, fstop=10)

SH = {"mag_closed": shot_mag_closed, "mag_open": shot_mag_open}
if __name__ == "__main__":
    name, out, w, h, spp = sys.argv[1], sys.argv[2], int(sys.argv[3]), int(sys.argv[4]), int(sys.argv[5])
    S.reset(); SH[name](); S.render(out, w, h, spp)
