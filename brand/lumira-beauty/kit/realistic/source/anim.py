"""Lumira box open/close loop: sleeve slides off, lid lifts, holds, closes. Renders PNG frames."""
import sys, os, math
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import scene as S
from scene import *
import box_mockup as B

def empty(name, children, parent=None):
    e = bpy.data.objects.new(name, None); bpy.context.scene.collection.objects.link(e)
    for o in children: o.parent = e
    if parent: e.parent = parent
    return e

def key(obj, f, loc=None, rot=None):
    if loc is not None: obj.location = loc; obj.keyframe_insert("location", frame=f)
    if rot is not None: obj.rotation_euler = tuple(math.radians(a) for a in rot); obj.keyframe_insert("rotation_euler", frame=f)

def build():
    stage(); world()
    root = empty("root", [])
    base = empty("base", B.base_with_products(), root)
    lid = empty("lid", B.lid_upright(), root)
    bw, bx = mm(43), mm(60)
    cream = m_paper("sleeve", (0.94, 0.915, 0.85, 1), 0.85)
    t = mm(0.6); D2, H2 = B.LD + 2 * t, B.LH + t   # hollow paper loop hugging the lid
    parts = [cube("sl_top", bw, D2, t, (0, 0, B.LH + t / 2), bevel=mm(0.25), m=cream),
             cube("sl_bot", bw, D2, t, (0, 0, -t / 2), bevel=mm(0.25), m=cream),
             cube("sl_front", bw, t, H2, (0, -B.LD / 2 - t / 2, B.LH / 2), bevel=mm(0.25), m=cream),
             cube("sl_back", bw, t, H2, (0, B.LD / 2 + t / 2, B.LH / 2), bevel=mm(0.25), m=cream)]
    band = plane("band_print", bw - mm(0.6), B.LD + mm(0.6), (0, 0, B.LH + t + mm(0.02)), m=m_decal_print("band", os.path.join(TEX, "band.png")))
    sleeve = empty("sleeve_e", parts + [band], root)
    root.rotation_euler = (0, 0, math.radians(-22))
    # timeline (24 fps, 120 frames)
    closed_l, open_l = (0, 0, 0), (mm(10), mm(70), mm(135))
    closed_r, open_r = (0, 0, 0), (16, 0, -4)
    s_on, s_off = (bx, 0, 0), (bx + mm(420), 0, 0)
    for f, sp in ((1, s_on), (10, s_on), (30, s_off), (96, s_off), (114, s_on), (120, s_on)):
        key(sleeve, f, loc=sp)
    for f, (l, r) in ((1, (closed_l, closed_r)), (32, (closed_l, closed_r)), (56, (open_l, open_r)), (74, (open_l, open_r)), (96, (closed_l, closed_r)), (120, (closed_l, closed_r))):
        key(lid, f, loc=l, rot=r)
    tgt = (0, mm(10), mm(70))
    B.lamp("key", (-0.55, -0.5, 0.85), tgt, 18, 0.9)
    B.lamp("fill", (0.7, -0.45, 0.32), tgt, 5, 1.2)
    B.lamp("rim", (0.3, 0.6, 0.6), tgt, 12, 0.6, color=(1, 0.95, 0.88))
    camera((0.27, -0.66, 0.50), tgt, lens=58, fstop=10)
    sc = bpy.context.scene; sc.frame_start, sc.frame_end = 1, 120; sc.render.fps = 24
    sc.render.use_motion_blur = True; sc.render.motion_blur_shutter = 0.5

if __name__ == "__main__":
    out, w, h, spp = sys.argv[1], int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    frames = sys.argv[5] if len(sys.argv) > 5 else None
    S.reset(); build()
    sc = bpy.context.scene
    sc.render.engine = "CYCLES"; sc.cycles.device = "CPU"; sc.cycles.samples = spp; sc.cycles.use_denoising = True
    sc.cycles.max_bounces = 8; sc.cycles.transparent_max_bounces = 12; sc.cycles.transmission_bounces = 8
    sc.render.resolution_x, sc.render.resolution_y = w, h
    try: sc.view_settings.view_transform = "Khronos PBR Neutral"
    except TypeError: sc.view_settings.view_transform = "AgX"
    sc.render.image_settings.file_format = "PNG"
    if frames:
        for f in [int(x) for x in frames.split(",")]:
            sc.frame_set(f); sc.render.filepath = f"{out}{f:04d}.png"; bpy.ops.render.render(write_still=True)
    else:
        sc.render.filepath = out; bpy.ops.render.render(animation=True)
