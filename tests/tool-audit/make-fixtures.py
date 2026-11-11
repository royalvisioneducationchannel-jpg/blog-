"""Generate audit fixtures (synthetic images, no real product data).
Usage: python3 make-fixtures.py <workdir>   -> creates <workdir>/fixtures and <workdir>/fixtures/big7
"""
import sys, os, shutil
import numpy as np
from PIL import Image, ImageDraw, ImageFilter
root = sys.argv[1]; FX = os.path.join(root, 'fixtures'); os.makedirs(os.path.join(FX, 'big7'), exist_ok=True); os.makedirs(os.path.join(root, 'out'), exist_ok=True)
rng = np.random.default_rng(7)
def product(w, h, bg=(235, 235, 235)):
    im = Image.new('RGB', (w, h), bg); d = ImageDraw.Draw(im)
    d.ellipse([w*.3, h*.2, w*.7, h*.8], fill=(36, 87, 214)); d.rectangle([w*.42, h*.1, w*.58, h*.25], fill=(40, 40, 40))
    a = np.clip(np.asarray(im, dtype=np.int16) + rng.normal(0, 18, (h, w, 3)).astype(np.int16), 0, 255).astype(np.uint8)
    return Image.fromarray(a).filter(ImageFilter.GaussianBlur(0.6))
p = lambda n: os.path.join(FX, n)
product(3000, 2000).save(p('photo-3000x2000.jpg'), quality=92)
product(1200, 1600).save(p('portrait-1200x1600.png'))
product(1000, 1000).save(p('square-1000.webp'), quality=90)
t = Image.new('RGBA', (800, 600), (0, 0, 0, 0)); ImageDraw.Draw(t).ellipse([200, 100, 600, 500], fill=(214, 60, 36, 255)); t.save(p('transparent-800x600.png'))
product(5000, 4000).save(p('large-5000x4000.jpg'), quality=97)
product(6500, 5000).save(p('huge-6500x5000-32MP.jpg'), quality=96)
product(7000, 6000).save(p('too-many-px-42MP.jpg'), quality=60)
for i in range(20): product(1600, 1200).save(p(f'batch-{i:02d}.jpg'), quality=88)
Image.new('RGB', (400, 300), (10, 200, 10)).save(p('anim.gif'))
open(p('corrupt.jpg'), 'wb').write(b'\xff\xd8\xff\xe0' + os.urandom(5000))
open(p('notes.txt'), 'w').write('not an image'); open(p('empty.jpg'), 'wb').close()
logo = Image.new('RGBA', (600, 200), (0, 0, 0, 0)); d = ImageDraw.Draw(logo); d.rectangle([0, 0, 599, 199], outline=(255, 255, 255, 255), width=12); d.text((40, 70), 'DEMO LOGO', fill=(255, 255, 255, 255)); logo.save(p('logo.png'))
Image.fromarray(rng.integers(0, 255, (3000, 2600, 3), dtype=np.uint8)).save(p('over-20mb-noise.png'), compress_level=0)
for k in range(1, 8): shutil.copy(p('huge-6500x5000-32MP.jpg'), os.path.join(FX, 'big7', f'h{k}.jpg'))
