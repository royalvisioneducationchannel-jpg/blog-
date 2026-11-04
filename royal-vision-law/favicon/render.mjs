// Render favicon.svg to the PNG sizes WordPress and browsers use.
import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';
import { dirname, join } from 'node:path';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
const here = dirname(fileURLToPath(import.meta.url));
const svg = readFileSync(join(here, 'favicon.svg'), 'utf8');
const b = await chromium.launch();
for (const size of [512, 180, 32, 16]) {
  const p = await b.newPage({ viewport: { width: size, height: size } });
  await p.setContent(`<html><body style="margin:0;background:transparent"><div style="width:${size}px;height:${size}px">${svg.replace('<svg ', '<svg width="100%" height="100%" ')}</div></body></html>`);
  await p.waitForTimeout(150);
  await p.screenshot({ path: join(here, `favicon-${size}.png`), omitBackground: true });
  await p.close();
}
await b.close();
