import sys, os
from PIL import Image, ImageDraw, ImageFont
A, OUT = sys.argv[1], sys.argv[2]
W, H = 2400, 3000
im = Image.new("RGB", (W, H), (246, 241, 231)); d = ImageDraw.Draw(im)
def F(path, s, w=None):
    f = ImageFont.truetype(os.path.join(A, path), s)
    if w:
        try: f.set_variation_by_axes([w])
        except Exception: pass
    return f
olive, gold, muted, line = (68, 78, 54), (168, 137, 90), (111, 115, 99), (210, 200, 180)
d.text((150, 140), "LUMIRA  ·  RITUAL KIT BOX", font=F("Jost.ttf", 34, 600), fill=gold)
d.text((150, 200), "Box size & fit", font=F("Marcellus-Regular.ttf", 120), fill=olive)
d.text((150, 350), "All sizes in mm. Product sizes are from the approved 3D model; confirm with real samples before ordering.", font=F("Jost.ttf", 34, 400), fill=muted)
y = 460
def section(t):
    global y
    d.line([(150, y), (W - 150, y)], fill=line, width=3); y += 30
    d.text((150, y), t.upper(), font=F("Jost.ttf", 32, 700), fill=gold); y += 70
def row(a, b, c=""):
    global y
    d.text((150, y), a, font=F("Jost.ttf", 40, 600), fill=olive)
    d.text((820, y), b, font=F("Jost.ttf", 40, 500), fill=olive)
    if c: d.text((1520, y), c, font=F("Jost.ttf", 34, 400), fill=muted)
    y += 74
section("Closed box (what the customer sees)")
row("Outer size", "243 x 178 x 67", "L x W x H, lid on")
row("Style", "Rigid 2-piece", "full-depth lid over base, 2 mm greyboard")
y += 20; section("Parts")
row("Lid", "243 x 178 x 67", "inside 239 x 174 x 65")
row("Base", "240 x 175 x 65", "inside 236 x 171 x 63")
row("Insert", "235 x 170 x 45", "EVA foam (paper-wrapped) or moulded pulp")
row("Sleeve (band)", "509 x 43 flat", "loop 494, cream 250-300 gsm")
row("Lid inside print", "238 x 173", "glued under the lid")
row("Ritual card", "105 x 148 (A6)", "350 gsm, sits in the lid")
y += 20; section("Products and their cavities")
row("Face wash tube", "Ø40 x 157 long", "lies flat · cavity 162 x 42, 25 deep · crimp slot 64")
row("Face serum", "Ø34 x 114 tall", "lies flat · cavity 120 x 36, 22 deep")
row("Moisturizer jar", "Ø66 x 54 tall", "stands up · cavity Ø68, 40 deep")
y += 20; section("Why 67 mm tall")
for t in ["The jar is the tallest item (54 mm). It stands in a 40 mm pocket on a 5 mm foam floor, so its lid ends 59 mm above the base floor.",
          "The tube lies with its top 60 mm above the floor. Inside height is 63 mm, so every product has at least 3 mm of room under the lid and card.",
          "Base 65 + 2 mm lid top = 67 mm closed. The sleeve slides over the closed box, 60 mm right of centre."]:
    d.text((150, y), "•  " + t, font=F("Jost.ttf", 36, 400), fill=olive); y += 64
y += 30; section("Files")
for t in ["1  Lid wrap (olive + gold foil plate, hidden layer)", "2  Base wrap (plain olive)", "3  Sleeve / belly band", "4  Lid inside print", "5  Insert cutting layout (1:1 top view)"]:
    d.text((150, y), t, font=F("Jost.ttf", 36, 500), fill=olive); y += 60
im.save(os.path.join(OUT, "Lumira-Box-0-Size-and-Fit.png"))
print("spec", y)
