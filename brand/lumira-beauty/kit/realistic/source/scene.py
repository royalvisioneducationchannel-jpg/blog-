"""Lumira ritual kit — photoreal product renders (Blender/Cycles via bpy).

python scene.py <shot> <out.png> [width height samples]
shots: hero, trio, open
"""
import bpy, bmesh, math, sys, os
from mathutils import Vector, Matrix

D = os.path.dirname(os.path.abspath(__file__))
TEX = os.path.join(D, "tex")
AS = os.path.join(D, "assets")
mm = lambda v: v / 1000.0

OLIVE = (0.050, 0.068, 0.034, 1)    # #444E36 in linear
OLIVE_L = (0.090, 0.115, 0.058, 1)
CREAM = (0.955, 0.925, 0.86, 1)
GOLD = (0.80, 0.60, 0.33, 1)


def reset():
    bpy.ops.wm.read_factory_settings(use_empty=True)


# ── materials ──────────────────────────────────────────────────────────────
def mat(name):
    m = bpy.data.materials.new(name)
    m.use_nodes = True
    nt = m.node_tree
    for n in list(nt.nodes):
        nt.nodes.remove(n)
    out = nt.nodes.new("ShaderNodeOutputMaterial")
    return m, nt, out


def bsdf(nt, color, rough, metal=0.0, coat=0.0, sheen=0.0, trans=0.0, ior=1.45, spec=0.5):
    b = nt.nodes.new("ShaderNodeBsdfPrincipled")
    b.inputs["Base Color"].default_value = color
    b.inputs["Roughness"].default_value = rough
    b.inputs["Metallic"].default_value = metal
    b.inputs["Coat Weight"].default_value = coat
    b.inputs["Sheen Weight"].default_value = sheen
    b.inputs["Transmission Weight"].default_value = trans
    b.inputs["IOR"].default_value = ior
    b.inputs["Specular IOR Level"].default_value = spec
    return b


def image(nt, path, extension="CLIP"):
    t = nt.nodes.new("ShaderNodeTexImage")
    t.image = bpy.data.images.load(path, check_existing=True)
    t.extension = extension
    t.interpolation = "Cubic"
    return t


def cyl_uv(nt, z0, h, turn=0.0):
    """Cylindrical mapping in object space: front (-Y) is u = 0.5."""
    tc = nt.nodes.new("ShaderNodeTexCoord")
    sep = nt.nodes.new("ShaderNodeSeparateXYZ")
    nt.links.new(tc.outputs["Object"], sep.inputs[0])
    negy = nt.nodes.new("ShaderNodeMath"); negy.operation = "MULTIPLY"; negy.inputs[1].default_value = -1
    nt.links.new(sep.outputs["Y"], negy.inputs[0])
    at = nt.nodes.new("ShaderNodeMath"); at.operation = "ARCTAN2"
    nt.links.new(sep.outputs["X"], at.inputs[0]); nt.links.new(negy.outputs[0], at.inputs[1])
    u = nt.nodes.new("ShaderNodeMath"); u.operation = "MULTIPLY_ADD"
    u.inputs[1].default_value = 1 / (2 * math.pi); u.inputs[2].default_value = 0.5 + turn
    nt.links.new(at.outputs[0], u.inputs[0])
    v = nt.nodes.new("ShaderNodeMath"); v.operation = "MULTIPLY_ADD"
    v.inputs[1].default_value = 1 / h; v.inputs[2].default_value = -z0 / h
    nt.links.new(sep.outputs["Z"], v.inputs[0])
    comb = nt.nodes.new("ShaderNodeCombineXYZ")
    nt.links.new(u.outputs[0], comb.inputs[0]); nt.links.new(v.outputs[0], comb.inputs[1])
    return comb.outputs[0]


def paper_bump(nt, b, scale=900.0, strength=0.05):
    n = nt.nodes.new("ShaderNodeTexNoise"); n.inputs["Scale"].default_value = scale; n.inputs["Detail"].default_value = 6
    bu = nt.nodes.new("ShaderNodeBump"); bu.inputs["Strength"].default_value = strength
    nt.links.new(n.outputs["Fac"], bu.inputs["Height"]); nt.links.new(bu.outputs["Normal"], b.inputs["Normal"])


def m_paper(name, color, rough=0.82, sheen=0.25):
    m, nt, out = mat(name)
    b = bsdf(nt, color, rough, sheen=sheen, spec=0.35)
    paper_bump(nt, b)
    nt.links.new(b.outputs[0], out.inputs[0])
    return m


def m_plastic(name, color, rough, coat=0.0):
    m, nt, out = mat(name)
    b = bsdf(nt, color, rough, coat=coat)
    nt.links.new(b.outputs[0], out.inputs[0])
    return m


def m_gold(name="gold", rough=0.22, ribs=0.0):
    m, nt, out = mat(name)
    b = bsdf(nt, GOLD, rough, metal=1.0)
    if ribs:
        w = nt.nodes.new("ShaderNodeTexWave"); w.wave_type = "BANDS"; w.bands_direction = "X"; w.inputs["Scale"].default_value = ribs
        bu = nt.nodes.new("ShaderNodeBump"); bu.inputs["Strength"].default_value = 0.25
        nt.links.new(w.outputs["Fac"], bu.inputs["Height"]); nt.links.new(bu.outputs["Normal"], b.inputs["Normal"])
    nt.links.new(b.outputs[0], out.inputs[0])
    return m


def m_decal_foil(name, path):
    """Gold foil where the image has alpha, see-through elsewhere."""
    m, nt, out = mat(name)
    t = image(nt, path)
    g = bsdf(nt, GOLD, 0.24, metal=1.0)
    tr = nt.nodes.new("ShaderNodeBsdfTransparent")
    mix = nt.nodes.new("ShaderNodeMixShader")
    nt.links.new(t.outputs["Alpha"], mix.inputs[0]); nt.links.new(tr.outputs[0], mix.inputs[1]); nt.links.new(g.outputs[0], mix.inputs[2])
    nt.links.new(mix.outputs[0], out.inputs[0])
    return m


def m_decal_print(name, path, rough=0.8):
    """Printed image (its own colours) where alpha, see-through elsewhere."""
    m, nt, out = mat(name)
    t = image(nt, path)
    b = bsdf(nt, CREAM, rough, sheen=0.2, spec=0.35)
    nt.links.new(t.outputs["Color"], b.inputs["Base Color"])
    paper_bump(nt, b, strength=0.03)
    tr = nt.nodes.new("ShaderNodeBsdfTransparent")
    mix = nt.nodes.new("ShaderNodeMixShader")
    nt.links.new(t.outputs["Alpha"], mix.inputs[0]); nt.links.new(tr.outputs[0], mix.inputs[1]); nt.links.new(b.outputs[0], mix.inputs[2])
    nt.links.new(mix.outputs[0], out.inputs[0])
    return m


def m_printed_cyl(name, base, rough, img, z0, h, foil=None, coat=0.0, see_through=False):
    """Cylinder surface with a wrapped print (and optional foil) mapped around it."""
    m, nt, out = mat(name)
    uv = cyl_uv(nt, z0, h)
    t = image(nt, img); nt.links.new(uv, t.inputs[0])
    b = bsdf(nt, base, rough, coat=coat)
    mixc = nt.nodes.new("ShaderNodeMix"); mixc.data_type = "RGBA"
    mixc.inputs["A"].default_value = base
    nt.links.new(t.outputs["Alpha"], mixc.inputs["Factor"]); nt.links.new(t.outputs["Color"], mixc.inputs["B"])
    nt.links.new(mixc.outputs["Result"], b.inputs["Base Color"])
    shader = b.outputs[0]
    if see_through:
        tr = nt.nodes.new("ShaderNodeBsdfTransparent")
        mx = nt.nodes.new("ShaderNodeMixShader")
        nt.links.new(t.outputs["Alpha"], mx.inputs[0]); nt.links.new(tr.outputs[0], mx.inputs[1]); nt.links.new(shader, mx.inputs[2])
        shader = mx.outputs[0]
    if foil:
        f = image(nt, foil); nt.links.new(uv, f.inputs[0])
        g = bsdf(nt, GOLD, 0.24, metal=1.0)
        mx = nt.nodes.new("ShaderNodeMixShader")
        nt.links.new(f.outputs["Alpha"], mx.inputs[0]); nt.links.new(shader, mx.inputs[1]); nt.links.new(g.outputs[0], mx.inputs[2])
        shader = mx.outputs[0]
    nt.links.new(shader, out.inputs[0])
    return m


def m_glass(name, color, rough):
    m, nt, out = mat(name)
    b = bsdf(nt, color, rough, trans=1.0, ior=1.5)
    nt.links.new(b.outputs[0], out.inputs[0])
    return m


# ── geometry helpers ───────────────────────────────────────────────────────
def link(obj, m=None):
    if m:
        obj.data.materials.append(m)
    return obj


def smooth(obj):
    for p in obj.data.polygons:
        p.use_smooth = True


def cube(name, sx, sy, sz, loc, bevel=0.0, m=None):
    bpy.ops.mesh.primitive_cube_add(size=1, location=loc)
    o = bpy.context.active_object; o.name = name
    o.scale = (sx, sy, sz); bpy.ops.object.transform_apply(scale=True)
    if bevel:
        mod = o.modifiers.new("bev", "BEVEL"); mod.width = bevel; mod.segments = 4; mod.limit_method = "ANGLE"
        smooth(o)
        o.data.shade_auto_smooth if hasattr(o.data, "shade_auto_smooth") else None
    return link(o, m)


def cyl(name, r, h, z0, m=None, verts=128, bevel=0.0, loc=(0, 0)):
    bpy.ops.mesh.primitive_cylinder_add(vertices=verts, radius=r, depth=h, location=(loc[0], loc[1], 0))
    o = bpy.context.active_object; o.name = name
    # origin stays on the floor, so object-space Z is the real height used by the print mapping
    o.data.transform(Matrix.Translation((0, 0, z0 + h / 2)))
    smooth(o)
    if bevel:
        mod = o.modifiers.new("bev", "BEVEL"); mod.width = bevel; mod.segments = 5; mod.limit_method = "ANGLE"
    # keep object origin at the cylinder axis, z = 0 at its base so print mapping uses real heights
    return link(o, m)


def plane(name, sx, sy, loc, rot=(0, 0, 0), m=None):
    bpy.ops.mesh.primitive_plane_add(size=1, location=loc, rotation=rot)
    o = bpy.context.active_object; o.name = name
    o.scale = (sx, sy, 1); bpy.ops.object.transform_apply(scale=True)
    return link(o, m)


def facing(p, cam):
    """Rotation (deg) that turns a product's front (-Y) toward the camera."""
    return math.degrees(math.atan2(cam[0] - p[0], -(cam[1] - p[1]))) - 0.0


def group(objs, loc, rot_z=0.0, name="grp"):
    e = bpy.data.objects.new(name, None); bpy.context.scene.collection.objects.link(e)
    for o in objs:
        o.parent = e
    e.location = loc; e.rotation_euler = (0, 0, math.radians(rot_z))
    return e


# ── products ───────────────────────────────────────────────────────────────
def tube(lay=False):
    """100 ml face-wash tube standing on its flip-top cap. Origin at the floor."""
    R, CAP, BODY, CRIMP = mm(20), mm(20), mm(128), mm(9)
    bm = bmesh.new(); seg = 160; rings = []
    nz = 90
    for i in range(nz + 1):
        f = i / nz; z = CAP + f * BODY
        shoulder = 0.74 + 0.26 * math.sin(min(1, f / 0.06) * math.pi / 2)
        t = max(0.0, (f - 0.72) / 0.28); t = t * t * (3 - 2 * t)
        rx, ry = R * shoulder * (1 + 0.5 * t), R * shoulder * (1 - 0.93 * t)
        ring = [bm.verts.new((rx * math.sin(2 * math.pi * k / seg), -ry * math.cos(2 * math.pi * k / seg), z)) for k in range(seg)]
        rings.append(ring)
    for a, b in zip(rings, rings[1:]):
        for k in range(seg):
            bm.faces.new((a[k], a[(k + 1) % seg], b[(k + 1) % seg], b[k]))
    bm.faces.new(list(reversed(rings[0]))); bm.faces.new(rings[-1])
    me = bpy.data.meshes.new("tube"); bm.to_mesh(me); bm.free()
    body = bpy.data.objects.new("tube_body", me); bpy.context.scene.collection.objects.link(body); smooth(body)
    body.data.materials.append(m_printed_cyl("tube_print", CREAM, 0.38, os.path.join(TEX, "tube.png"), CAP, BODY, foil=os.path.join(TEX, "tubefoil.png"), coat=0.15))
    crimp = cube("tube_crimp", R * 2 * 1.5 + mm(2), mm(2.6), CRIMP, (0, 0, CAP + BODY + CRIMP / 2 - mm(0.5)), bevel=mm(0.6))
    cm, nt, out = mat("crimp"); b = bsdf(nt, CREAM, 0.45)
    w = nt.nodes.new("ShaderNodeTexWave"); w.wave_type = "BANDS"; w.bands_direction = "X"; w.inputs["Scale"].default_value = 60
    bu = nt.nodes.new("ShaderNodeBump"); bu.inputs["Strength"].default_value = 0.35
    nt.links.new(w.outputs["Fac"], bu.inputs["Height"]); nt.links.new(bu.outputs["Normal"], b.inputs["Normal"]); nt.links.new(b.outputs[0], out.inputs[0])
    crimp.data.materials.append(cm)
    olive = m_plastic("cap_olive", OLIVE_L, 0.3, coat=0.3)
    cap = cyl("tube_cap", mm(15.2), CAP - mm(0.4), mm(0.4), olive, bevel=mm(1.2))
    seam = cyl("tube_cap_seam", mm(15.35), mm(0.5), mm(13.5), m_plastic("seam", (0.02, 0.025, 0.015, 1), 0.5))
    return [body, crimp, cap, seam]


def serum():
    """30 ml frosted dropper. Origin at the floor."""
    R, H = mm(17), mm(72)
    glass = cyl("serum_glass", R, H, 0, m_glass("frost", (0.86, 0.91, 0.82, 1), 0.32), bevel=mm(4))
    liquid = cyl("serum_liquid", R - mm(2.2), H * 0.72, mm(2.5), m_glass("serum_liquid", (1.0, 0.86, 0.55, 1), 0.05))
    label = cyl("serum_label", R + mm(0.25), H, 0, m_printed_cyl("serum_print", CREAM, 0.75, os.path.join(TEX, "serum.png"), 0, H, see_through=True))
    neck = cyl("serum_neck", mm(8), mm(6), H, m_glass("frost2", (0.86, 0.91, 0.82, 1), 0.32))
    collar = cyl("serum_collar", mm(10.5), mm(17), H + mm(2), m_gold("collar_gold", 0.2, ribs=380.0), bevel=mm(0.8))
    rub = m_plastic("bulb", OLIVE_L, 0.55)
    bulb = cyl("serum_bulb", mm(7), mm(16), H + mm(19), rub, bevel=mm(0.5))
    bpy.ops.mesh.primitive_uv_sphere_add(radius=mm(7), location=(0, 0, H + mm(35)), segments=64, ring_count=32)
    top = bpy.context.active_object; top.name = "serum_bulb_top"; smooth(top); top.data.materials.append(rub)
    return [glass, liquid, label, neck, collar, bulb, top]


def jar():
    """50 g double-wall jar with olive lid. Origin at the floor."""
    R, H, LH = mm(31.5), mm(38), mm(16)
    body = cyl("jar_body", R, H, 0, m_printed_cyl("jar_print", (0.965, 0.945, 0.9, 1), 0.16, os.path.join(TEX, "jar.png"), 0, H, coat=0.6), bevel=mm(3))
    lid = cyl("jar_lid", R + mm(1.5), LH, H - mm(0.5), m_printed_cyl("jar_lid", OLIVE_L, 0.28, os.path.join(TEX, "jarlid.png"), H - mm(0.5), LH, coat=0.35), bevel=mm(1.6))
    s = mm(36)
    top = plane("jar_lid_mark", s, s * 715 / 671, (0, 0, H - mm(0.5) + LH + mm(0.08)), m=m_decal_foil("jar_foil", os.path.join(AS, "mark-gold.png")))
    return [body, lid, top]


def kit_box(lid_only=False):
    """Closed 240 x 175 x 65 mm rigid box with sleeve and foil. Origin at the floor, centre."""
    W, Dp, H = mm(240), mm(175), mm(65)
    olive = m_paper("box_olive", OLIVE, 0.72, sheen=0.35)
    box = cube("box", W, Dp, H, (0, 0, H / 2), bevel=mm(1.2), m=olive)
    bw, bx = mm(43), mm(60)
    cream = m_paper("sleeve", (0.94, 0.915, 0.85, 1), 0.85)
    sleeve = cube("sleeve", bw, Dp + mm(1.2), H + mm(1.2), (bx, 0, (H + mm(1.2)) / 2 - mm(0.6)), bevel=mm(0.5), m=cream)
    band = plane("band_print", bw - mm(0.6), Dp + mm(0.6), (bx, 0, H + mm(0.62)), m=m_decal_print("band", os.path.join(TEX, "band.png")))
    band.rotation_euler = (0, 0, 0)
    mw = mm(96)
    mark = plane("lid_mark", mw, mw * 715 / 671, (mm(-34), 0, H + mm(0.05)), m=m_decal_foil("lid_foil", os.path.join(AS, "mark-gold.png")))
    ww = mm(92)
    word = plane("front_word", ww, ww * 186 / 1202, (mm(-34), -Dp / 2 - mm(0.05), H * 0.52), rot=(math.radians(90), 0, 0), m=m_decal_foil("word_foil", os.path.join(AS, "word-gold.png")))
    return [box, sleeve, band, mark, word]


# ── stage ──────────────────────────────────────────────────────────────────
def stage(color=(0.52, 0.47, 0.40, 1)):
    """Cyclorama: floor sweeping up into a back wall, no horizon line."""
    bm = bmesh.new(); prof = []
    for i in range(40):
        y = -1.2 + i * (1.2 + 0.25) / 39; prof.append((y, 0.0))
    for i in range(1, 25):
        a = i / 24 * math.pi / 2; prof.append((0.25 + 0.35 * math.sin(a), 0.35 - 0.35 * math.cos(a)))
    for i in range(1, 12):
        prof.append((0.6, 0.35 + i * 0.12))
    cols = []
    for x in (-2.0, 2.0):
        cols.append([bm.verts.new((x, y, z)) for y, z in prof])
    for k in range(len(prof) - 1):
        bm.faces.new((cols[0][k], cols[1][k], cols[1][k + 1], cols[0][k + 1]))
    me = bpy.data.meshes.new("cyc"); bm.to_mesh(me); bm.free()
    o = bpy.data.objects.new("cyc", me); bpy.context.scene.collection.objects.link(o); smooth(o)
    m, nt, out = mat("stage"); b = bsdf(nt, color, 0.62, spec=0.3)
    paper_bump(nt, b, scale=300, strength=0.03)
    nt.links.new(b.outputs[0], out.inputs[0]); o.data.materials.append(m)
    return o


def light(name, loc, target, power, size, color=(1, 0.97, 0.92)):
    ld = bpy.data.lights.new(name, "AREA"); ld.energy = power; ld.size = size; ld.color = color; ld.shape = "DISK"
    o = bpy.data.objects.new(name, ld); bpy.context.scene.collection.objects.link(o)
    o.location = loc
    d = Vector(target) - Vector(loc); o.rotation_euler = d.to_track_quat("-Z", "Y").to_euler()
    return o


def camera(loc, target, lens=70, fstop=5.6, focus=None):
    cd = bpy.data.cameras.new("cam"); cd.lens = lens
    cd.dof.use_dof = True; cd.dof.aperture_fstop = fstop
    o = bpy.data.objects.new("cam", cd); bpy.context.scene.collection.objects.link(o)
    o.location = loc
    d = Vector(target) - Vector(loc); o.rotation_euler = d.to_track_quat("-Z", "Y").to_euler()
    cd.dof.focus_distance = focus if focus else d.length
    bpy.context.scene.camera = o
    return o


def world(color=(0.86, 0.83, 0.77), strength=0.12):
    w = bpy.data.worlds.new("w"); bpy.context.scene.world = w; w.use_nodes = True
    bg = w.node_tree.nodes["Background"]; bg.inputs[0].default_value = (*color, 1); bg.inputs[1].default_value = strength


def render(out, w, h, samples):
    s = bpy.context.scene
    s.render.engine = "CYCLES"; s.cycles.device = "CPU"; s.cycles.samples = samples
    s.cycles.use_denoising = True
    s.cycles.max_bounces = 10; s.cycles.transparent_max_bounces = 16; s.cycles.transmission_bounces = 10
    s.render.resolution_x = w; s.render.resolution_y = h; s.render.resolution_percentage = 100
    try:
        s.view_settings.view_transform = "Khronos PBR Neutral"
    except TypeError:
        s.view_settings.view_transform = "AgX"
    s.render.image_settings.file_format = "PNG"
    s.render.filepath = out
    s.render.threads_mode = "AUTO"
    bpy.ops.render.render(write_still=True)


# ── shots ──────────────────────────────────────────────────────────────────
def shot_hero():
    stage(); world()
    group(kit_box(), (mm(-50), mm(40), 0), rot_z=-14, name="box")
    group(tube(), (mm(120), mm(-40), 0), rot_z=-8, name="tube")
    group(serum(), (mm(175), mm(-5), 0), rot_z=-12, name="serum")
    group(jar(), (mm(55), mm(-120), 0), rot_z=-10, name="jar")
    tgt = (mm(30), mm(-15), mm(50))
    light("key", (-0.55, -0.55, 0.75), tgt, 18, 0.9)
    light("fill", (0.7, -0.45, 0.35), tgt, 5, 1.2)
    light("rim", (0.25, 0.55, 0.6), tgt, 12, 0.6, color=(1, 0.95, 0.88))
    camera((0.26, -0.80, 0.42), tgt, lens=60, fstop=8)


def shot_trio():
    stage(); world()
    cam = (0.0, -0.70, 0.16)
    for fn, p in ((tube, (mm(-72), 0)), (serum, (0, mm(10))), (jar, (mm(76), mm(-10)))):
        group(fn(), (*p, 0), rot_z=facing(p, cam), name=fn.__name__)
    tgt = (0, 0, mm(62))
    light("key", (-0.5, -0.6, 0.55), tgt, 16, 0.9)
    light("fill", (0.6, -0.5, 0.3), tgt, 4, 1.2)
    light("rim", (0.0, 0.5, 0.55), tgt, 12, 0.7)
    camera(cam, tgt, lens=85, fstop=9)



def boolean(target, cutters):
    for c in cutters:
        mod = target.modifiers.new("cut", "BOOLEAN"); mod.operation = "DIFFERENCE"; mod.object = c; mod.solver = "EXACT"
        c.hide_render = True; c.hide_viewport = True
        c.display_type = "WIRE"


def place(objs, loc, rot):
    e = bpy.data.objects.new("p", None); bpy.context.scene.collection.objects.link(e)
    for o in objs:
        o.parent = e
    e.location = loc; e.rotation_euler = tuple(math.radians(a) for a in rot)
    return e


def shot_open():
    stage(); world()
    W, Dp, H, TT = mm(240), mm(175), mm(60), mm(50)
    olive = m_paper("box_olive", OLIVE, 0.72, sheen=0.35)
    # base: open-top olive shell
    base = cube("base", W, Dp, H, (0, 0, H / 2), bevel=mm(1.0), m=olive)
    inner = cube("base_in", W - mm(4), Dp - mm(4), H, (0, 0, H / 2 + mm(3)))
    boolean(base, [inner])
    # cream insert tray with cavities for the three products
    tray = cube("tray", W - mm(4.4), Dp - mm(4.4), TT - mm(3), (0, 0, mm(3) + (TT - mm(3)) / 2), m=m_paper("tray", (0.93, 0.9, 0.83, 1), 0.9))
    cut_tube = cyl("cut_tube", mm(21.5), mm(168), 0); cut_tube.rotation_euler = (0, math.radians(90), 0); cut_tube.location = (mm(-84), mm(42), TT)
    cut_crimp = cube("cut_crimp", mm(14), mm(66), mm(10), (mm(72), mm(42), TT))
    cut_ser = cyl("cut_ser", mm(18.5), mm(128), 0); cut_ser.rotation_euler = (0, math.radians(90), 0); cut_ser.location = (mm(-104), mm(-42), TT)
    cut_jar = cyl("cut_jar", mm(34.5), mm(40), TT - mm(24), loc=(mm(62), mm(-40)))
    boolean(tray, [cut_tube, cut_crimp, cut_ser, cut_jar])
    # products resting in the tray, fronts up
    place(tube(), (mm(-82), mm(42), TT), (-90, 0, -90))
    place(serum(), (mm(-100), mm(-42), TT), (-90, 0, -90))
    place(jar(), (mm(62), mm(-40), TT - mm(24)), (0, 0, -12))
    base_grp = [base, tray]
    # lid, upside down behind the base, showing its printed inside and the ritual card
    LW, LD, LH = mm(243), mm(178), mm(65)
    lid = cube("lid", LW, LD, LH, (0, 0, LH / 2), bevel=mm(1.0), m=olive)
    lin = cube("lid_in", LW - mm(4), LD - mm(4), LH, (0, 0, LH / 2 + mm(2.5)))
    boolean(lid, [lin])
    print_in = plane("lid_print", LW - mm(4.2), LD - mm(4.2), (0, 0, mm(2.55)), m=m_decal_print("lidin", os.path.join(TEX, "lidin.png"), rough=0.75))
    card = plane("card", mm(94), mm(133), (0, 0, 0), m=m_decal_print("card", os.path.join(TEX, "card.png"), rough=0.7))
    # plane() bakes its creation transform into the mesh, so the card is made at the origin and moved here
    card.location = (mm(52), mm(2), mm(2.9)); card.rotation_euler = (0, 0, math.radians(-5))
    place([lid, lin, print_in, card], (mm(18), mm(205), 0), (0, 0, 4))
    tgt = (mm(10), mm(98), mm(22))
    light("key", (-0.55, -0.35, 0.9), tgt, 22, 1.0)
    light("fill", (0.7, -0.2, 0.5), tgt, 6, 1.2)
    light("rim", (0.1, 0.8, 0.7), tgt, 10, 0.8)
    camera((0.02, -0.40, 0.74), tgt, lens=54, fstop=11)


def shot_detail():
    """Macro on the lid: foil mark, paper grain and the sleeve edge."""
    stage(); world()
    group(kit_box(), (0, 0, 0), rot_z=-28, name="box")
    group(serum(), (mm(150), mm(-70), 0), rot_z=-20, name="serum")
    tgt = (mm(-20), mm(-10), mm(62))
    light("key", (-0.45, -0.2, 0.55), tgt, 12, 0.5)
    light("fill", (0.5, -0.5, 0.3), tgt, 3, 1.0)
    light("rim", (0.2, 0.5, 0.4), tgt, 10, 0.5)
    camera((0.12, -0.36, 0.24), tgt, lens=100, fstop=3.2, focus=None)


SHOTS = {"hero": shot_hero, "trio": shot_trio, "open": shot_open, "detail": shot_detail}

if __name__ == "__main__":
    argv = sys.argv[sys.argv.index("--") + 1:] if "--" in sys.argv else sys.argv[1:]
    name, out = argv[0], argv[1]
    w, h, spp = (int(argv[2]), int(argv[3]), int(argv[4])) if len(argv) > 4 else (640, 800, 32)
    reset()
    SHOTS[name]()
    render(out, w, h, spp)
