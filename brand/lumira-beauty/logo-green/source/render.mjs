import { createRequire } from 'module';
const require = createRequire('/opt/node-tools/node_modules/');
const { chromium } = require('playwright');
import fs from 'fs';
const P = JSON.parse(fs.readFileSync('paths.json', 'utf8'));
const GREEN_C = '#55623F', GREEN_E = '#36402B', GOLD = '#D9C094', WHITE = '#FFFFFF';
// variants: [file, W, H, parts, dropColor, bg, layout]
const ONLY = process.argv[2];
const V0 = [
  ['Lumira-Logo-Green-White.png', 2048, 2048, ['mark','drop','word','sub'], WHITE, true, 'center'],
  ['Lumira-Logo-Green-White-GoldDrop.png', 2048, 2048, ['mark','drop','word','sub'], GOLD, true, 'center'],
  ['Lumira-FB-Profile-Mark.png', 1080, 1080, ['mark','drop'], GOLD, true, 'profile'],
  ['Lumira-FB-Profile-Mark-White.png', 1080, 1080, ['mark','drop'], WHITE, true, 'profile'],
  ['Lumira-Logo-White-Transparent.png', 2048, 2048, ['mark','drop','word','sub'], WHITE, false, 'center'],
  ['Lumira-FB-Cover-1640x624.png', 1640, 624, ['mark','drop','word','sub'], GOLD, true, 'cover'],
];
const V = ONLY ? V0.filter(v => v[0].includes(ONLY)) : V0;
const browser = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium' });
const page = await browser.newPage(); page.on('console', m => console.log('console', m.text())); page.on('pageerror', e => console.log('err', e.message));
fs.mkdirSync('out', { recursive: true });
for (const [file, W, H, parts, drop, bg, layout] of V) {
  const paths = parts.map(k => `<path id="${k}" fill-rule="evenodd" fill="${k==='drop'?drop:WHITE}" d="${P[k]}"/>`).join('');
  const html = `<html><body style="margin:0;background:transparent">
  <svg id="s" xmlns="http://www.w3.org/2000/svg" width="${W}" height="${H}" viewBox="0 0 ${W} ${H}">
   <defs><radialGradient id="g" cx="50%" cy="45%" r="75%"><stop offset="0" stop-color="${GREEN_C}"/><stop offset="1" stop-color="${GREEN_E}"/></radialGradient></defs>
   ${bg ? `<rect width="${W}" height="${H}" fill="url(#g)"/>` : ''}
   <g id="art">${paths}</g></svg>
  <script>{
   const art=document.getElementById('art'), b=art.getBBox(), W=${W}, H=${H}, L='${layout}';
   let s, tx, ty;
   if (L==='center'){ s=Math.min(W*0.62/b.width, H*0.62/b.height); }
   else if (L==='profile'){ s=Math.min(W*0.50/b.width, H*0.56/b.height); }
   else { s=Math.min(W*0.40/b.width, H*0.62/b.height); }
   tx=W/2-(b.x+b.width/2)*s; ty=H/2-(b.y+b.height/2)*s;
   if (L==='profile') tx += 0.03*W*0; 
   console.log(L, s, tx, ty, b.width, b.height); art.setAttribute('transform','translate('+tx+','+ty+') scale('+s+')');
  }</script></body></html>`;
  await page.setViewportSize({ width: W, height: H });
  await page.setContent(html);
  await page.locator('#s').screenshot({ path: 'out/' + file, omitBackground: !bg });
  if (file.includes('Transparent') === false && file === 'Lumira-Logo-Green-White-GoldDrop.png') fs.writeFileSync('out/Lumira-Logo.svg', html.match(/<svg[\s\S]*<\/svg>/)[0]);
  console.log(file);
}
await browser.close();
