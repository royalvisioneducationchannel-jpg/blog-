import math
G='#D4B77E'
def courthouse():
    o=[]
    a=o.append
    a(f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 480 540" fill="none" stroke="{G}" stroke-width="1.6" stroke-linejoin="round" stroke-linecap="round">')
    # arch field + frames
    a('<path d="M24 540V236C24 117 121 20 240 20s216 97 216 216v304z" fill="#0B1F1B" fill-opacity=".72" stroke="none"/>')
    a('<path d="M24 540V236C24 117 121 20 240 20s216 97 216 216v304" opacity=".55"/>')
    a('<path d="M40 540V238C40 128 130 38 240 38s200 90 200 200v302" opacity=".28"/>')
    # rays from pediment apex
    rays=[]
    for i in range(13):
        ang=math.radians(200+i*(140/12))
        x2=240+math.cos(ang)*170; y2=168+math.sin(ang)*120
        x1=240+math.cos(ang)*40; y1=168+math.sin(ang)*28
        rays.append(f'M{x1:.1f} {y1:.1f}L{x2:.1f} {y2:.1f}')
    a(f'<path d="{"".join(rays)}" stroke-width="1" opacity=".22"/>')
    # star / sun
    a('<circle cx="240" cy="112" r="5" fill="'+G+'" fill-opacity=".9" stroke="none"/>')
    # pediment
    a('<path d="M92 252L240 172l148 80z" fill="'+G+'" fill-opacity=".07"/>')
    a('<path d="M122 244L240 182l118 62z" opacity=".55"/>')
    # scales in tympanum
    a('<path d="M240 192v42M228 236h24M214 206h52M240 196m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/>')
    a('<path d="M214 206l-8 15M214 206l8 15M201 221a13 5 0 0 0 26 0zM266 206l-8 15M266 206l8 15M253 221a13 5 0 0 0 26 0z" stroke-width="1.3"/>')
    # entablature
    a('<rect x="86" y="252" width="308" height="14"/><rect x="98" y="266" width="284" height="12"/>')
    a('<path d="'+''.join(f'M{x} 282h6v6h-6z' for x in range(104,376,12))+'" stroke-width="1" opacity=".75"/>')
    # columns
    for c in range(125,356,46):
        a(f'<path d="M{c-17} 292h34M{c-14} 298h28" />')
        a(f'<path d="M{c-17} 292c-4 0-6 3-6 6M{c+17} 292c4 0 6 3 6 6" stroke-width="1.2"/>')
        a(f'<rect x="{c-12}" y="300" width="24" height="150"/>')
        a(f'<path d="M{c-6} 306v138M{c} 306v138M{c+6} 306v138" stroke-width=".8" opacity=".6"/>')
        a(f'<rect x="{c-15}" y="450" width="30" height="7"/>')
    # steps
    a('<rect x="88" y="457" width="304" height="13"/><rect x="72" y="470" width="336" height="13"/><rect x="56" y="483" width="368" height="13"/>')
    a('<path d="M24 506h432" opacity=".5"/><path d="M60 518h360" opacity=".25"/>')
    a('</svg>')
    return ''.join(o)

def scales(stroke=G, sw=1.6):
    return (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none" stroke="{stroke}" stroke-width="{sw}" stroke-linecap="round" stroke-linejoin="round">'
      '<path d="M32 8v44M22 56h20M26 52h12M12 18h40M32 12a2.5 2.5 0 1 0 0 .1"/>'
      '<path d="M12 18l-7 16M12 18l7 16M4 34a8 3.5 0 0 0 16 0zM52 18l-7 16M52 18l7 16M44 34a8 3.5 0 0 0 16 0z"/></svg>')

def column(stroke=G):
    # tall ionic column watermark 120x520
    return (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 140 560" fill="none" stroke="{stroke}" stroke-width="2" stroke-linejoin="round" stroke-linecap="round">'
      '<path d="M6 30h128M14 44h112"/><path d="M14 30c-10 0-12 14-2 18s14-4 8-10M126 30c10 0 12 14 2 18s-14-4-8-10"/>'
      '<rect x="2" y="10" width="136" height="20"/><path d="M30 52h80"/>'
      '<rect x="34" y="56" width="72" height="440"/>'
      '<path d="M46 64v424M58 64v424M70 64v424M82 64v424M94 64v424" stroke-width="1.2" opacity=".7"/>'
      '<rect x="26" y="496" width="88" height="16"/><rect x="16" y="512" width="108" height="18"/><rect x="6" y="530" width="128" height="20"/></svg>')

def mini_column(stroke=G):
    return (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 40" fill="none" stroke="{stroke}" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round">'
      '<path d="M3 6L16 1l13 5zM4 6h24v3H4zM8 9v24M13 9v24M19 9v24M24 9v24M5 33h22v3H5zM3 36h26v3H3z"/></svg>')

def gavel(stroke=G):
    return (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220 220" fill="none" stroke="{stroke}" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round">'
      '<g transform="rotate(-40 110 110)"><rect x="62" y="40" width="96" height="42" rx="6"/><rect x="52" y="46" width="10" height="30" rx="3"/><rect x="158" y="46" width="10" height="30" rx="3"/>'
      '<path d="M80 40v42M140 40v42" opacity=".6"/><rect x="104" y="82" width="12" height="104" rx="5"/></g>'
      '<rect x="40" y="184" width="140" height="14" rx="4"/><rect x="56" y="172" width="108" height="12" rx="4"/></svg>')

def laurel(stroke=G):
    cx,cy,r=80,58,48
    parts=[]
    for side in (1,-1):
        th0,th1=(100,232) if side==1 else (80,-52)
        a0,a1=math.radians(th0),math.radians(th1)
        x0,y0=cx+r*math.cos(a0),cy+r*math.sin(a0); x1,y1=cx+r*math.cos(a1),cy+r*math.sin(a1)
        sweep=1 if side==1 else 0
        parts.append(f'<path d="M{x0:.1f} {y0:.1f}A{r} {r} 0 0 {sweep} {x1:.1f} {y1:.1f}"/>')
        n=8
        for i in range(n):
            th=math.radians(th0+(th1-th0)*(i+0.6)/n)
            px,py=cx+r*math.cos(th),cy+r*math.sin(th)
            tx,ty=-math.sin(th)*side,math.cos(th)*side
            tang=math.degrees(math.atan2(ty,tx))
            for k in (1,-1):
                ang=tang+k*38
                lx=px+math.cos(math.radians(ang))*7; ly=py+math.sin(math.radians(ang))*7
                parts.append(f'<ellipse cx="{lx:.1f}" cy="{ly:.1f}" rx="7.5" ry="2.8" transform="rotate({ang:.1f} {lx:.1f} {ly:.1f})"/>')
        parts.append(f'<ellipse cx="{x1+ (math.cos(a1+side*1.4))*0:.1f}" cy="{y1:.1f}" rx="2" ry="2" fill="{stroke}"/>')
    return (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 120" fill="none" stroke="{stroke}" stroke-width="1.3">'+''.join(parts)+'</svg>')

def doorway():
    o=[]; a=o.append
    a(f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 480 540" fill="none" stroke="{G}" stroke-width="1.6" stroke-linejoin="round" stroke-linecap="round">')
    a('<defs><radialGradient id="g" cx="50%" cy="46%" r="55%"><stop offset="0" stop-color="#E9D3A2" stop-opacity=".34"/><stop offset=".55" stop-color="#B08D57" stop-opacity=".1"/><stop offset="1" stop-color="#B08D57" stop-opacity="0"/></radialGradient></defs>')
    a('<path d="M24 540V236C24 117 121 20 240 20s216 97 216 216v304z" fill="#0B1F1B" fill-opacity=".5" stroke="none"/>')
    a('<path d="M24 540V236C24 117 121 20 240 20s216 97 216 216v304z" fill="url(#g)" stroke="none"/>')
    a('<path d="M24 540V236C24 117 121 20 240 20s216 97 216 216v304" opacity=".7"/>')
    a('<path d="M40 540V238C40 128 130 38 240 38s200 90 200 200v302" opacity=".35"/>')
    rays=[]
    for i in range(17):
        ang=math.radians(196+i*(148/16))
        x1=240+math.cos(ang)*70; y1=230+math.sin(ang)*70
        x2=240+math.cos(ang)*196; y2=230+math.sin(ang)*190
        rays.append(f'M{x1:.1f} {y1:.1f}L{x2:.1f} {y2:.1f}')
    a(f'<path d="{"".join(rays)}" stroke-width="1" opacity=".3"/>')
    # keystone medallion with scales
    a('<circle cx="240" cy="62" r="22" fill="#0B1F1B" stroke-width="1.4"/><circle cx="240" cy="62" r="17" opacity=".5"/>')
    a('<path d="M240 50v22M233 74h14M229 55h22M229 55l-4 8M229 55l4 8M222 63a7 3 0 0 0 14 0M251 55l-4 8M251 55l4 8M244 63a7 3 0 0 0 14 0" stroke-width="1.2"/>')
    # side columns
    for c in (82,398):
        a(f'<path d="M{c-22} 200h44M{c-18} 208h36M{c-22} 200c-5 0-7 4-7 8M{c+22} 200c5 0 7 4 7 8" />')
        a(f'<rect x="{c-15}" y="210" width="30" height="262"/>')
        a(f'<path d="M{c-8} 216v250M{c} 216v250M{c+8} 216v250" stroke-width=".8" opacity=".6"/>')
        a(f'<rect x="{c-19}" y="472" width="38" height="8"/>')
    a('<path d="M60 192h360" opacity=".45"/><path d="M60 184h360" opacity=".25"/>')
    # steps
    a('<rect x="44" y="480" width="392" height="14"/><rect x="34" y="494" width="412" height="14" opacity=".8"/><rect x="24" y="508" width="432" height="14" opacity=".6"/>')
    a('</svg>')
    return ''.join(o)

def rays():
    o=[f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 500" fill="none" stroke="{G}" stroke-width="1" stroke-linecap="round">']
    d=''
    for i in range(25):
        ang=math.radians(180+i*(180/24))
        x1=200+math.cos(ang)*60; y1=190+math.sin(ang)*60
        x2=200+math.cos(ang)*330; y2=190+math.sin(ang)*330
        d+=f'M{x1:.1f} {y1:.1f}L{x2:.1f} {y2:.1f}'
    o.append(f'<path d="{d}" opacity=".22"/>')
    o.append('<circle cx="200" cy="190" r="120" opacity=".14"/><circle cx="200" cy="190" r="150" opacity=".08"/>')
    o.append('</svg>')
    return ''.join(o)

open('rays.svg','w').write(rays())
open('doorway.svg','w').write(doorway())
open('courthouse.svg','w').write(courthouse())
open('scales.svg','w').write(scales())
open('column.svg','w').write(column())
open('mini-column.svg','w').write(mini_column())
open('gavel.svg','w').write(gavel())
open('laurel.svg','w').write(laurel())
