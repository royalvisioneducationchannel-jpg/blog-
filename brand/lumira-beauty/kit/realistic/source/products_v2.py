"""Lumira product line v2: face-wash tube, moisturizer tube (olive), serum dropper — new labels."""
import sys, os, math
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import scene as S
from scene import *
import box_mockup as B

CREAM_CAP = (0.90, 0.86, 0.77, 1)

def tube2(name, tex, foil, body_col, cap_col, R=mm(20), CAP=mm(20), BODY=mm(128), CRIMP=mm(9)):
    bm = bmesh.new(); seg = 160; rings = []; nz = 90
    for i in range(nz + 1):
        f = i / nz; z = CAP + f * BODY
        shoulder = 0.74 + 0.26 * math.sin(min(1, f / 0.06) * math.pi / 2)
        t = max(0.0, (f - 0.72) / 0.28); t = t * t * (3 - 2 * t)
        rx, ry = R * shoulder * (1 + 0.5 * t), R * shoulder * (1 - 0.93 * t)
        rings.append([bm.verts.new((rx * math.sin(2 * math.pi * k / seg), -ry * math.cos(2 * math.pi * k / seg), z)) for k in range(seg)])
    for a, b in zip(rings, rings[1:]):
        for k in range(seg):
            bm.faces.new((a[k], a[(k + 1) % seg], b[(k + 1) % seg], b[k]))
    bm.faces.new(list(reversed(rings[0]))); bm.faces.new(rings[-1])
    me = bpy.data.meshes.new(name); bm.to_mesh(me); bm.free()
    body = bpy.data.objects.new(name + "_body", me); bpy.context.scene.collection.objects.link(body); smooth(body)
    body.data.materials.append(m_printed_cyl(name + "_print", body_col, 0.38, os.path.join(TEX, tex), CAP, BODY, foil=os.path.join(TEX, foil), coat=0.15))
    crimp = cube(name + "_crimp", R * 2 * 1.5 + mm(2), mm(2.6), CRIMP, (0, 0, CAP + BODY + CRIMP / 2 - mm(0.5)), bevel=mm(0.6))
    cm, nt, out = mat(name + "_crimp"); b = bsdf(nt, body_col, 0.45)
    w = nt.nodes.new("ShaderNodeTexWave"); w.wave_type = "BANDS"; w.bands_direction = "X"; w.inputs["Scale"].default_value = 60
    bu = nt.nodes.new("ShaderNodeBump"); bu.inputs["Strength"].default_value = 0.35
    nt.links.new(w.outputs["Fac"], bu.inputs["Height"]); nt.links.new(bu.outputs["Normal"], b.inputs["Normal"]); nt.links.new(b.outputs[0], out.inputs[0])
    crimp.data.materials.append(cm)
    capm = m_plastic(name + "_cap", cap_col, 0.3, coat=0.3)
    cap = cyl(name + "_cap", R * 0.76, CAP - mm(0.4), mm(0.4), capm, bevel=mm(1.2))
    seam = cyl(name + "_seam", R * 0.7675, mm(0.5), CAP * 0.675, m_plastic(name + "_seamm", (0.02, 0.025, 0.015, 1), 0.5))
    return [body, crimp, cap, seam]

def face_wash(): return tube2("fw", "fw.png", "fwfoil.png", CREAM, OLIVE_L)
def moisturizer(): return tube2("mo", "mo.png", "mofoil.png", OLIVE_L, CREAM_CAP, R=mm(17.5), CAP=mm(18), BODY=mm(112), CRIMP=mm(8))

def serum2():
    R, H = mm(17), mm(72)
    glass = cyl("serum_glass", R, H, 0, m_glass("frost", (0.86, 0.91, 0.82, 1), 0.32), bevel=mm(4))
    liquid = cyl("serum_liquid", R - mm(2.2), H * 0.72, mm(2.5), m_glass("serum_liquid", (1.0, 0.86, 0.55, 1), 0.05))
    label = cyl("serum_label", R + mm(0.25), H, 0, m_printed_cyl("serum_print", CREAM, 0.75, os.path.join(TEX, "se.png"), 0, H, see_through=True))
    neck = cyl("serum_neck", mm(8), mm(6), H, m_glass("frost2", (0.86, 0.91, 0.82, 1), 0.32))
    collar = cyl("serum_collar", mm(10.5), mm(17), H + mm(2), m_gold("collar_gold", 0.2, ribs=380.0), bevel=mm(0.8))
    rub = m_plastic("bulb", OLIVE_L, 0.55)
    bulb = cyl("serum_bulb", mm(7), mm(16), H + mm(19), rub, bevel=mm(0.5))
    bpy.ops.mesh.primitive_uv_sphere_add(radius=mm(7), location=(0, 0, H + mm(35)), segments=64, ring_count=32)
    top = bpy.context.active_object; top.name = "serum_bulb_top"; smooth(top); top.data.materials.append(rub)
    return [glass, liquid, label, neck, collar, bulb, top]

def shot_line():
    stage(); world()
    cam = (0.0, -0.70, 0.16)
    for fn, p in ((face_wash, (mm(-74), 0)), (serum2, (0, mm(12))), (moisturizer, (mm(72), mm(-6)))):
        group(fn(), (*p, 0), rot_z=facing(p, cam), name=fn.__name__)
    tgt = (0, 0, mm(64))
    B.lamp("key", (-0.5, -0.6, 0.55), tgt, 16, 0.9)
    B.lamp("fill", (0.6, -0.5, 0.3), tgt, 4, 1.2)
    B.lamp("rim", (0.0, 0.5, 0.55), tgt, 12, 0.7)
    camera(cam, tgt, lens=85, fstop=9)

def _single(fn, zc, dist):
    stage(); world()
    group(fn(), (0, 0, 0), rot_z=0, name="p")
    tgt = (0, 0, zc)
    B.lamp("key", (-0.5, -0.6, 0.55), tgt, 14, 0.9)
    B.lamp("fill", (0.6, -0.5, 0.3), tgt, 4, 1.2)
    B.lamp("rim", (0.0, 0.5, 0.55), tgt, 10, 0.7)
    camera((0, -dist, mm(70)), tgt, lens=85, fstop=8)

SH = {"line": shot_line,
      "single_fw": lambda: _single(face_wash, mm(80), 0.56),
      "single_mo": lambda: _single(moisturizer, mm(70), 0.52),
      "single_se": lambda: _single(serum2, mm(55), 0.48)}
if __name__ == "__main__":
    name, out, w, h, spp = sys.argv[1], sys.argv[2], int(sys.argv[3]), int(sys.argv[4]), int(sys.argv[5])
    S.reset(); SH[name](); S.render(out, w, h, spp)
