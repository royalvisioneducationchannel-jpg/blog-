import csv, sys
src, tpl, out = sys.argv[1:4]
toks = [x for x in csv.DictReader(open(src)) if x['text'].strip()]
roman = """Ji dosto, | Buds Pro 6 hamare paas | restock + ho chuka hai. | To der kis baat ki? | Abhi order karein! |
Is ke - - features ki baat karein. | Is ke andar | aap ko multiple features | ke saath different colors | dekhne ko mil jaate hain. |
Sab se pehle | colors ki baat karte hain: | Red color | aap ko dekhne ko mil jaata hai, | White color mein bhi | available hai, | Black color mein bhi, |
aur saath saath | aap ke paas | Yellow color mein bhi | dekhne ko mil jaate hain. | Agar aap log | is offer se | faida uthana chahte hain, |
20% off lag chuka hai! | To abhi | hamari website + ko visit karein | aur apna order - place karein. | Aur sab se - important baat: |
free shipping + ke saath mil rahe hain | all over Pakistan mein. | Thank you!""".split()
breaks=set(); clean=[]
for r in roman:
    if r=='|': breaks.add(len(clean)-1)
    else: clean.append(r)
assert len(clean)==len(toks),(len(clean),len(toks))
words=[]
for ti,(r,t) in enumerate(zip(clean,toks)):
    s,e=int(t['start'])/1000,int(t['end'])/1000
    if r=='-': continue
    if r=='+': words[-1]['end']=e; words[-1]['ti']=ti; continue
    words.append({'text':r,'start':s,'end':e,'ti':ti})
for i,w in enumerate(words):
    if w['end']-w['start']<0.08 and i:
        w['start']=max(w['end']-0.1,words[i-1]['start']+0.06); words[i-1]['end']=min(words[i-1]['end'],w['start'])
groups,cur=[],[]
for i,w in enumerate(words):
    cur.append(w); nxt=words[i+1] if i+1<len(words) else None
    if nxt is None or any(w['ti']<=b<nxt['ti'] for b in breaks): groups.append(cur); cur=[]
html,js=[],[]
SKIP={25.56}  # the pause card spells this line out itself
for gi,g in enumerate(groups):
    if round(g[0]['start'],2) in SKIP: continue
    gs=max(0.12,g[0]['start']-0.05)
    ns=groups[gi+1][0]['start']-0.05 if gi+1<len(groups) else 30.0
    ge=min(g[-1]['end']+0.3,ns)
    gid=f"cg{gi}"; spans=[]
    for wi,w in enumerate(g):
        wid=f"{gid}w{wi}"
        KEY={'buds','pro','6','restock','order','colors','red','white','black','yellow','20%','off','website','free','shipping','pakistan'}
        iskey=w['text'].lower().strip(',.!?:') in KEY
        ul=f'<i id="{wid}u" class="ul"></i>' if iskey else ''
        spans.append(f'<span class="m{" key" if iskey else ""}"><span id="{wid}" class="cw">{w["text"]}</span>{ul}</span>')
        if iskey: js.append(f'key("#{wid}u",{w["start"]:.2f});')
        js.append(f'hit("#{wid}",{w["start"]:.2f});')
    html.append(f'    <div id="{gid}" class="clip cgroup" data-start="{gs:.2f}" data-duration="{ge-gs:.2f}" data-track-index="0"><span class="cline">{" ".join(spans)}</span></div>')
    js.append(f'grp("#{gid}",{gs:.2f},{ge:.2f});')
open(out,'w').write(open(tpl).read().replace('<!--GROUPS-->','\n'.join(html)).replace('//STEPS','\n    '.join(js)))
print(len(groups),'groups')
for g in groups: print(f"{g[0]['start']:.2f}-{g[-1]['end']:.2f}",' '.join(w['text'] for w in g))
