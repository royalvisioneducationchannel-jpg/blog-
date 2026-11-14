import { createRequire } from 'module'; const require = createRequire('/opt/node-tools/node_modules/');
const { chromium } = require('playwright');
const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium' });
const p = await b.newPage({ viewport: { width: 1640, height: 624 }, deviceScaleFactor: +(process.argv[2]||1) });
await p.goto('file://' + process.cwd() + '/cover.html'); await p.waitForTimeout(300);
await p.locator('#c').screenshot({ path: process.argv[3] || 'cover_preview.png' }); await b.close();
