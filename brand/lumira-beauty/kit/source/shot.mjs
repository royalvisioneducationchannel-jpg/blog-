import { chromium } from '/opt/node-tools/node_modules/playwright/index.mjs';
const dir = process.argv[2];
const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium' });
const p = await b.newPage({ viewport: { width: 1600, height: 2000 } });
await p.goto(`file://${dir}/kit.html`, { waitUntil: 'networkidle' });
await p.evaluate(() => document.fonts.ready);
for (const id of ['b1', 'b2', 'b3', 'b4']) {
  await p.locator('#' + id).screenshot({ path: `${dir}/out/${id}.png` });
}
await p.pdf({ path: `${dir}/out/kit.pdf`, width: '1600px', height: '2040px', printBackground: true });
await b.close();
