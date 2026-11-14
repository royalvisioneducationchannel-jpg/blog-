"""Lumira products on travertine podiums with leaves, transparent film + shadow catcher, for compositing onto the green Facebook cover."""
import sys, os, math, random
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import scene as S
from scene import *
import box_mockup as B
import products_v2 as P

def m_travertine(name):
    m, nt, out = mat(name); b = bsdf(nt, (0.78, 0.70, 0.58, 1), 0.72, spec=0.35)
    tc = nt.nodes.new("ShaderNodeTexCoord")
    n = nt.nodes.new("ShaderNodeTexNoise"); n.inputs["Scale"].default_value = 18; n.inputs["Detail"].default_value = 6
    w = nt.nodes.new("ShaderNodeTexWave"); w.wave_type = "BANDS"; w.inputs["Scale"].default_value = 6; w.inputs["Distortion"].default_value = 6
    w.inputs["Detail"].default_value = 4
    nt.links.new(tc.outputs["Object"], n.inputs["Vector"]); nt.links.new(tc.outputs["Object"], w.inputs["Vector"])
    mix = nt.nodes.new("ShaderNodeMix"); mix.data_type = "FLOAT"; mix.inputs["Factor"].default_value = 0.5
    nt.links.new(n.outputs["Fac"], mix.inputs["A"]); nt.links.new(w.outputs["Fac"], mix.inputs["B"])
    ramp = nt.nodes.new("ShaderNodeValToRGB")
    ramp.color_ramp.elements[0].color = (0.66, 0.57, 0.45, 1); ramp.color_ramp.elements[1].color = (0.86, 0.80, 0.69, 1)
    nt.links.new(mix.outputs["Result"], ramp.inputs["Fac"]); nt.links.new(ramp.outputs["Color"], b.inputs["Base Color"])
    v = nt.nodes.new("ShaderNodeTexVoronoi"); v.inputs["Scale"].default_value = 260
    nt.links.new(tc.outputs["Object"], v.inputs["Vector"])
    bu = nt.nodes.new("ShaderNodeBump"); bu.inputs["Strength"].default_value = 0.25; bu.inputs["Distance"].default_value = 0.0004
    nt.links.new(v.outputs["Distance"], bu.inputs["Height"]); nt.links.new(bu.outputs["Normal"], b.inputs["Normal"])
    nt.links.new(b.outputs[0], out.inputs[0]); return m

def m_leaf(name, col):
    m, nt, out = mat(name); b = bsdf(nt, col, 0.42, coat=0.25)
    try: b.inputs["Subsurface Weight"].default_value = 0.15; b.inputs["Subsurface Radius"].default_value = (0.02, 0.05, 0.01)
    except KeyError: pass
    tc = nt.nodes.new("ShaderNodeTexCoord"); w = nt.nodes.new("ShaderNodeTexWave"); w.inputs["Scale"].default_value = 30
    w.wave_type = "BANDS"; w.bands_direction = "X"
    nt.links.new(tc.outputs["UV"], w.inputs["Vector"])
    bu = nt.nodes.new("ShaderNodeBump"); bu.inputs["Strength"].default_value = 0.08
    nt.links.new(w.outputs["Fac"], bu.inputs["Height"]); nt.links.new(bu.outputs["Normal"], b.inputs["Normal"])
    nt.links.new(b.outputs[0], out.inputs[0]); return m

def leaf(name, L, W, loc, rot, curl, m):
    bm = bmesh.new(); nu, nv = 40, 12; grid = []
    uv = bm.loops.layers.uv.new()
    for i in range(nu + 1):
        u = i / nu; half = W * (math.sin(math.pi * min(u, 0.999)) ** 0.75) * (1 - 0.25 * u)
        row = []
        for j in range(nv + 1):
            v = -1 + 2 * j / nv; y = v * half
            z = curl * (u * L) ** 2 / L - abs(v) * half * 0.18
            row.append(bm.verts.new((u * L, y, z)))
        grid.append(row)
    for i in range(nu):
        for j in range(nv):
            f = bm.faces.new((grid[i][j], grid[i + 1][j], grid[i + 1][j + 1], grid[i][j + 1]))
            for l, (a, c) in zip(f.loops, ((i, j), (i + 1, j), (i + 1, j + 1), (i, j + 1))):
                l[uv].uv = (a / nu, c / nv)
    me = bpy.data.meshes.new(name); bm.to_mesh(me); bm.free()
    o = bpy.data.objects.new(name, me); bpy.context.scene.collection.objects.link(o); smooth(o)
    sol = o.modifiers.new("t", "SOLIDIFY"); sol.thickness = mm(0.6)
    o.data.materials.append(m); o.location = loc; o.rotation_euler = tuple(math.radians(r) for r in rot)
    return o

def build():
    world((0.42, 0.52, 0.32), 0.5)
    s = bpy.context.scene; s.render.film_transparent = True
    try: s.cycles.film_transparent_glass = True
    except AttributeError: pass
    floor = plane("floor", 3, 3, (0, 0, 0)); floor.is_shadow_catcher = True
    trav = m_travertine("trav")
    pods = [((mm(-62), mm(18)), mm(62), mm(46)), ((mm(8), mm(-40)), mm(54), mm(20)), ((mm(70), mm(14)), mm(56), mm(32))]
    for k, ((x, y), r, h) in enumerate(pods):
        cyl(f"pod{k}", r, h, 0, trav, verts=192, bevel=mm(2.5), loc=(x, y))
    cam = (mm(-10), -0.66, 0.15)
    for fn, (x, y), z in ((P.face_wash, (mm(-62), mm(18)), mm(46)), (P.serum2, (mm(8), mm(-40)), mm(20)), (P.moisturizer, (mm(70), mm(14)), mm(32))):
        group(fn(), (x, y, z), rot_z=facing((x, y), cam), name=fn.__name__)
    # keep printed labels matte so the small type stays readable at cover size
    for m in bpy.data.materials:
        if m.name in ("fw_print", "serum_print") and m.node_tree:
            for n in m.node_tree.nodes:
                if n.type == "BSDF_PRINCIPLED" and n.inputs["Metallic"].default_value < 0.5:
                    n.inputs["Coat Weight"].default_value = 0.0
                    n.inputs["Specular IOR Level"].default_value = 0.18
                    n.inputs["Roughness"].default_value = 0.7
                elif n.type == "BSDF_PRINCIPLED":  # gold foil: deeper tone so it reads on cream
                    n.inputs["Base Color"].default_value = (0.42, 0.27, 0.09, 1)
                    n.inputs["Roughness"].default_value = 0.32
    g1, g2 = m_leaf("leafA", (0.035, 0.085, 0.022, 1)), m_leaf("leafB", (0.07, 0.13, 0.04, 1))
    leaf("l1", mm(115), mm(26), (mm(-105), mm(55), mm(2)), (6, -8, 165), 0.25, g1)
    leaf("l2", mm(105), mm(24), (mm(118), mm(60), mm(2)), (5, -10, 30), 0.3, g2)
    leaf("l3", mm(90), mm(22), (mm(-30), mm(-75), mm(1)), (4, -6, -150), 0.10, g2)
    leaf("l4", mm(95), mm(22), (mm(55), mm(-70), mm(1)), (3, -8, -30), 0.12, g1)
    leaf("l5", mm(140), mm(30), (mm(-120), mm(95), mm(2)), (10, -22, 70), 0.4, g1)
    tgt = (mm(5), 0, mm(88))
    B.lamp("key", (-0.55, -0.5, 0.6), tgt, 13, 1.2, (1.0, 0.96, 0.90))
    B.lamp("fill", (0.45, -0.7, 0.25), tgt, 5, 1.6, (0.95, 1.0, 0.92))
    B.lamp("rim", (0.45, 0.45, 0.5), tgt, 9, 0.6, (1.0, 0.96, 0.85))
    B.lamp("rimL", (-0.5, 0.45, 0.4), tgt, 4, 0.6, (0.9, 1.0, 0.85))
    camera(cam, tgt, lens=78, fstop=16)
    bpy.context.scene.view_settings.exposure = 0.25

if __name__ == "__main__":
    out, w, h, spp = sys.argv[1], int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4])
    S.reset(); build(); S.render(out, w, h, spp)
