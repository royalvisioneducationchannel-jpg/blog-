import { chromium } from '/opt/node-tools/node_modules/playwright/index.mjs';
const dir = process.argv[2];
const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium' });
const p = await b.newPage({ viewport: { width: 3200, height: 2400 } });
await p.goto(`file://${dir}/textures_v2.html`, { waitUntil: 'networkidle' });
await p.evaluate(() => document.fonts.ready);
for (const id of ['fw', 'fwfoil', 'mo', 'mofoil', 'se'])
  await p.locator('#' + id).screenshot({ path: `${dir}/tex/${id}.png`, omitBackground: true });
await b.close();
