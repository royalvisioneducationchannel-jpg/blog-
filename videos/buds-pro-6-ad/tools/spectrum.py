"""Per-frame spectrum of the soundtrack (as placed in the edit) -> bar heights for the sound-wave graphic."""
import json, sys
import numpy as np
raw, out = sys.argv[1], sys.argv[2]
sr, fps, nb = 22050, 30, 32
x = np.fromfile(raw, dtype=np.float32)
edges = np.geomspace(60, 9000, nb + 1)
freqs = np.fft.rfftfreq(2048, 1 / sr)
win = np.hanning(2048)
frames = []
for f in range(int(len(x) / sr * fps)):
    c = int(f / fps * sr); seg = x[max(0, c - 1024):c + 1024]
    if len(seg) < 2048: seg = np.pad(seg, (0, 2048 - len(seg)))
    mag = np.abs(np.fft.rfft(seg * win))
    frames.append([mag[(freqs >= lo) & (freqs < hi)].mean() for lo, hi in zip(edges[:-1], edges[1:])])
a = 20 * np.log10(np.array(frames) + 1e-6)
lo, hi = np.percentile(a, 5, axis=0), np.percentile(a, 99, axis=0)
v = np.clip((a - lo) / (hi - lo), 0, 1)
# keep only the two scenes that draw the wave: 0-3.05 s and 29.75-33 s
keep = {str(i): [int(round(q * 20)) for q in v[i]] for i in range(len(v)) if i <= 92 or i >= 892}
json.dump(keep, open(out, 'w'), separators=(',', ':'))
print(len(v), 'frames,', len(keep), 'kept')
