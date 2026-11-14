import sys, numpy as np, onnxruntime as ort
from PIL import Image
sess = ort.InferenceSession("birefnet_lite.onnx", providers=["CPUExecutionProvider"])
inp = sess.get_inputs()[0].name
for src, dst in zip(sys.argv[1::2], sys.argv[2::2]):
    im = Image.open(src).convert("RGB")
    x = np.asarray(im.resize((1024, 1024), Image.BILINEAR), dtype=np.float32) / 255.
    x = (x - [0.485, 0.456, 0.406]) / [0.229, 0.224, 0.225]
    x = x.transpose(2, 0, 1)[None].astype(np.float32)
    out = sess.run(None, {inp: x})[-1][0, 0]
    if out.min() < 0 or out.max() > 1: out = 1 / (1 + np.exp(-out))
    m = Image.fromarray((out * 255).clip(0, 255).astype(np.uint8)).resize(im.size, Image.BILINEAR)
    rgba = im.copy(); rgba.putalpha(m); rgba.save(dst); print(dst, im.size, float(out.mean()))
