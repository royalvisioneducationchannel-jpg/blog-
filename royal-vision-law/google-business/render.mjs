// Render every html/*.html to a 2x PNG next to this script.
import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';
import { readdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
const here = dirname(fileURLToPath(import.meta.url));
const b = await chromium.launch();
for (const f of readdirSync(join(here, 'html')).filter(f => f.endsWith('.html'))) {
  const p = await b.newPage({ deviceScaleFactor: 2, viewport: { width: 1200, height: 800 } });
  await p.goto('file://' + join(here, 'html', f), { waitUntil: 'load' });
  await p.evaluate(() => document.fonts.ready);
  await p.waitForTimeout(300);
  await p.locator('#art').screenshot({ path: join(here, f.replace('.html', '.png')) });
  await p.close();
}
await b.close();
