"""Travertine podiums + leaves only (no products), transparent film with shadow catcher.
Prints each podium's top-centre pixel and px/mm so HD product photos can be placed on them."""
import sys, os, math, json
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import scene as S
from scene import *
import box_mockup as B
from cover_stage import m_travertine, m_leaf, leaf
from bpy_extras.object_utils import world_to_camera_view

PODS = {"fw": ((-78, 22), 50, 42), "se": ((4, -48), 46, 16), "mo": ((82, 26), 54, 66)}

def build():
    world((0.42, 0.52, 0.32), 0.5)
    s = bpy.context.scene; s.render.film_transparent = True
    floor = plane("floor", 3, 3, (0, 0, 0)); floor.is_shadow_catcher = True
    trav = m_travertine("trav")
    for k, ((x, y), r, h) in PODS.items():
        cyl("pod_" + k, mm(r), mm(h), 0, trav, verts=192, bevel=mm(2.5), loc=(mm(x), mm(y)))
    g1, g2 = m_leaf("leafA", (0.035, 0.085, 0.022, 1)), m_leaf("leafB", (0.07, 0.13, 0.04, 1))
    leaf("l1", mm(120), mm(26), (mm(-120), mm(55), mm(2)), (6, -8, 165), 0.25, g1)
    leaf("l2", mm(110), mm(24), (mm(135), mm(70), mm(2)), (5, -10, 30), 0.3, g2)
    leaf("l3", mm(90), mm(22), (mm(-35), mm(-85), mm(1)), (4, -6, -150), 0.10, g2)
    leaf("l4", mm(95), mm(22), (mm(60), mm(-80), mm(1)), (3, -8, -30), 0.12, g1)
    cam = (0, -1.12, 0.27); tgt = (0, 0, mm(95))
    B.lamp("key", (-0.55, -0.5, 0.6), tgt, 13, 1.2, (1.0, 0.96, 0.90))
    B.lamp("fill", (0.45, -0.7, 0.25), tgt, 5, 1.6, (0.95, 1.0, 0.92))
    B.lamp("rim", (0.45, 0.45, 0.5), tgt, 9, 0.6, (1.0, 0.96, 0.85))
    camera(cam, tgt, lens=70, fstop=16)
    s.view_settings.exposure = 0.25

def report(w, h):
    s = bpy.context.scene; cam = s.camera; out = {}
    for k, ((x, y), r, hh) in PODS.items():
        c = world_to_camera_view(s, cam, Vector((mm(x), mm(y), mm(hh))))
        e = world_to_camera_view(s, cam, Vector((mm(x) + mm(r), mm(y), mm(hh))))
        out[k] = {"x": c.x * w, "y": (1 - c.y) * h, "px_per_mm": (e.x - c.x) * w / r}
    print("PODS_JSON", json.dumps(out))

if __name__ == "__main__":
    o, w, h, spp = sys.argv[1], int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    S.reset(); build(); S.render(o, w, h, spp); report(w, h)
