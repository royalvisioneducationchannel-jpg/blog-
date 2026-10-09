import sys
from PIL import Image, ImageDraw, ImageFilter, ImageEnhance
W,H=960,1200
src=sys.argv[1] + '/%d.webp'  # transparent studio cut-outs supplied by the firm
# n: (slug, head_top, chin_y, face_cx)
P={10:('arshad-mehmood-warraich',50,530,548),11:('younis-amin',60,520,540),12:('shahid-amin',44,490,565),
   13:('rana-tahir-mehmood',16,600,490),14:('advocate-pending',47,470,560)}
HEAD=500; TOP=96  # target head height (hair top->chin) and hair-top y on canvas
def backdrop():
    bg=Image.new('RGB',(W,H))
    top,bot=(242,238,229),(214,207,193)
    d=ImageDraw.Draw(bg)
    for y in range(H):
        t=y/(H-1); d.line([(0,y),(W,y)],fill=tuple(round(top[i]+(bot[i]-top[i])*t) for i in range(3)))
    glow=Image.new('L',(W,H),0); ImageDraw.Draw(glow).ellipse((W*.12,H*.02,W*.88,H*.62),fill=255)
    glow=glow.filter(ImageFilter.GaussianBlur(140))
    bg=Image.composite(Image.new('RGB',(W,H),(250,248,243)),bg,glow)
    vig=Image.new('L',(W,H),0); ImageDraw.Draw(vig).rectangle((0,0,W,H),fill=0); ImageDraw.Draw(vig).ellipse((-W*.25,-H*.1,W*1.25,H*1.2),fill=255)
    vig=vig.filter(ImageFilter.GaussianBlur(160))
    return Image.composite(bg,Image.new('RGB',(W,H),(196,188,172)),vig)
bg0=backdrop()
for n,(slug,ht,chin,cx) in P.items():
    im=Image.open(src%n).convert('RGBA')
    a=im.getchannel('A').point(lambda v: min(255,round(v*255/250)))
    im.putalpha(a)
    s=max(HEAD/(chin-ht),W/im.width+0.01); sw,sh=round(im.width*s),round(im.height*s)
    ox=round(W/2-cx*s); oy=round(TOP-ht*s)
    ox=max(W-sw,min(0,ox))           # body must reach both side edges
    oy=min(oy,H-sh) if oy+sh<H else oy
    sub=im.resize((sw,sh),Image.LANCZOS)
    out=bg0.copy(); out.paste(sub,(ox,oy),sub)
    out=ImageEnhance.Sharpness(out).enhance(1.15)
    out.save(f'{sys.argv[2]}/{slug}.jpg',quality=88,optimize=True,progressive=True)
    print(n,slug,round(s,3),(ox,oy),(sw,sh),'bottom',oy+sh)
