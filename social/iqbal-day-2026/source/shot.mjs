import { chromium } from '/opt/node-tools/node_modules/playwright/index.mjs';
const [dir, out, scale] = [process.argv[2], process.argv[3], Number(process.argv[4] || 1)];
const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium' });
const p = await b.newPage({ viewport: { width: 1080, height: 1350 }, deviceScaleFactor: scale });
await p.goto(`file://${dir}/post.html`, { waitUntil: 'networkidle' });
await p.evaluate(() => document.fonts.ready);
await p.screenshot({ path: out, type: out.endsWith('.png') ? 'png' : 'jpeg', ...(out.endsWith('.png') ? {} : { quality: 92 }) });
await b.close();
