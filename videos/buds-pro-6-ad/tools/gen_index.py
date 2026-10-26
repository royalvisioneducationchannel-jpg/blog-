import json, math
kf = json.load(open('tools/kf.json'))
# label anchor (cam space) = bottom-centre of the label; side chosen so the leader avoids the face
C = {
  'red':    dict(name='Red',    sw='#e5483e', start=11.36, end=13.30, lab=(800, 330), line_end=13.30, track_from=11.36),
  'white':  dict(name='White',  sw='#f4f4f2', start=13.32, end=14.72, lab=(270, 330), line_end=14.0,  track_from=13.32),
  'black':  dict(name='Black',  sw='#2a2e38', start=14.74, end=17.12, lab=(800, 330), line_end=17.12, track_from=16.27),
  'yellow': dict(name='Yellow', sw='#f3d417', start=17.14, end=18.70, lab=(270, 330), line_end=18.70, track_from=17.14),
}
html, js = [], []
for k, c in C.items():
    t = kf[k]; R = round(0.6 * max(t['w'], t['h'])); pts = t['kf']
    s, e = c['start'], c['end']; lx, ly = c['lab']
    html.append(f'''        <div id="co-{k}" class="clip co" data-start="{s}" data-duration="{round(e-s,2)}" data-track-index="{4 + list(C).index(k) % 2}">
          <svg class="co-svg" width="1080" height="1920" viewBox="0 0 1080 1920">
            <line id="co-{k}-l1" class="lead-under" x1="{lx}" y1="{ly}" x2="{lx}" y2="{ly}" />
            <line id="co-{k}-l2" class="lead" x1="{lx}" y1="{ly}" x2="{lx}" y2="{ly}" />
            <g id="co-{k}-ring" transform="translate({pts[0][1]} {pts[0][2]})">
              <g id="co-{k}-spin">
                <circle class="ring-under" r="{R}" />
                <circle id="co-{k}-rc" class="ring" r="{R}" stroke-dasharray="{round(2*math.pi*R)}" stroke-dashoffset="{round(2*math.pi*R)}" />
                <line class="tick" x1="0" y1="{-R-14}" x2="0" y2="{-R+8}" /><line class="tick" x1="{R-8}" y1="0" x2="{R+14}" y2="0" />
                <line class="tick" x1="0" y1="{R-8}" x2="0" y2="{R+14}" /><line class="tick" x1="{-R-14}" y1="0" x2="{-R+8}" y2="0" />
              </g>
            </g>
          </svg>
          <div id="co-{k}-lab" class="co-lab" style="left: {lx - 230}px; top: {ly - 150}px">
            <div class="co-kick">Color</div>
            <div class="co-name"><span class="co-sw" style="background: {c['sw']}"></span>{c['name']}</div>
          </div>
        </div>''')
    def edge(cx, cy):
        dx, dy = lx - cx, ly - cy; d = math.hypot(dx, dy) or 1
        return round(cx + dx / d * (R + 16)), round(cy + dy / d * (R + 16))
    tf = c['track_from']
    js.append(f'// {k}')
    js.append(f'tl.fromTo("#co-{k}-lab", {{ opacity: 0, y: 16 }}, {{ opacity: 1, y: 0, duration: 0.35, ease: E }}, {s});')
    js.append(f'tl.to("#co-{k}-lab", {{ opacity: 0, duration: 0.14, ease: "power2.in" }}, {round(e - 0.14, 2)});')
    js.append(f'tl.fromTo("#co-{k}-rc", {{ attr: {{ "stroke-dashoffset": {round(2*math.pi*R)} }} }}, {{ attr: {{ "stroke-dashoffset": 0 }}, duration: 0.45, ease: "power2.out" }}, {tf});')
    js.append(f'tl.fromTo("#co-{k}-ring", {{ opacity: 0 }}, {{ opacity: 1, duration: 0.15, ease: "none" }}, {tf});')
    js.append(f'tl.fromTo("#co-{k}-spin", {{ rotation: 0 }}, {{ rotation: 35, duration: {round(e - tf, 2)}, ease: "none", svgOrigin: "0 0" }}, {tf});')
    js.append(f'tl.to("#co-{k}-ring", {{ opacity: 0, duration: 0.14, ease: "none" }}, {round(e - 0.14, 2)});')
    # ring follows the track
    prev = None
    for (t0, x, y) in pts:
        t0 = max(t0, tf)
        if prev is None:
            js.append(f'tl.set("#co-{k}-ring", {{ attr: {{ transform: "translate({x} {y})" }} }}, {s});')
        else:
            js.append(f'tl.to("#co-{k}-ring", {{ attr: {{ transform: "translate({x} {y})" }}, duration: {round(t0 - prev, 3)}, ease: "none" }}, {prev});')
        prev = t0
    # leader: grows from the label to the ring edge, then follows
    ex, ey = edge(pts[0][1], pts[0][2])
    js.append(f'tl.fromTo(["#co-{k}-l1", "#co-{k}-l2"], {{ attr: {{ x2: {lx}, y2: {ly} }} }}, {{ attr: {{ x2: {ex}, y2: {ey} }}, duration: 0.3, ease: "power2.out" }}, {round(tf + 0.12, 2)});')
    prev = round(tf + 0.42, 3)
    for (t0, x, y) in pts:
        if t0 <= prev or t0 > c['line_end'] - 0.18: continue
        ex, ey = edge(x, y)
        js.append(f'tl.to(["#co-{k}-l1", "#co-{k}-l2"], {{ attr: {{ x2: {ex}, y2: {ey} }}, duration: {round(t0 - prev, 3)}, ease: "none" }}, {prev});')
        prev = t0
    js.append(f'tl.to(["#co-{k}-l1", "#co-{k}-l2"], {{ attr: {{ x2: {lx}, y2: {ly} }}, duration: 0.18, ease: "power2.in" }}, {round(min(c["line_end"], e) - 0.18, 2)});')

tpl = open('tools/index.tpl').read()
open('index.html', 'w').write(tpl.replace('<!--CALLOUTS-->', '\n'.join(html)).replace('//CALLOUTS', '\n      '.join(js)))
print('ok', len(js))
