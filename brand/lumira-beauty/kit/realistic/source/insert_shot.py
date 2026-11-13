import sys, os, math
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import scene as S
from scene import *
import box_mockup as B, magnetic as M

def build(products):
    stage(); world()
    root = bpy.data.objects.new("root", None); bpy.context.scene.collection.objects.link(root)
    parts = B.shell("base", M.BW, M.BD, M.BH, B.FLOOR, B.olive_mat(), up=True) + M.insert_with_products()
    for o in parts:
        if o.parent is None: o.parent = root
    if not products:   # keep the foam and ribbon, hide the three products
        for e in [o for o in parts if o.type == "EMPTY"]:
            for c in e.children_recursive: c.hide_render = True
    root.rotation_euler = (0, 0, math.radians(-12))
    tgt = (0, 0, mm(40))
    B.lamp("key", (-0.25, -0.2, 0.9), tgt, 30, 1.2)
    B.lamp("fill", (0.6, -0.4, 0.5), tgt, 6, 1.2)
    B.lamp("rim", (0.2, 0.6, 0.7), tgt, 8, 0.8)
    camera((0.03, -0.20, 0.58), tgt, lens=58, fstop=11)

if __name__ == "__main__":
    which, out, w, h, spp = sys.argv[1], sys.argv[2], int(sys.argv[3]), int(sys.argv[4]), int(sys.argv[5])
    S.reset(); build(which == "full"); S.render(out, w, h, spp)
