const { chromium } = require('playwright');
const S = process.argv[2]; const B = 'https://lightyellow-alligator-410734.hostingersite.com';
const paths = ['/', '/tools/', '/tools/product-image-tools/', '/tools/image-resizer/', '/tools/watermark/', '/tools/profit-calculator/', '/tools/invoice-generator/', '/tools/utm-builder/'];
(async () => {
  const b = await chromium.launch();
  for (const w of [360, 768, 1024]) {
    const ctx = await b.newContext({ viewport: { width: w, height: 800 } });
    const pg = await ctx.newPage();
    for (const p of paths) {
      await pg.goto(B + p, { waitUntil: 'networkidle' });
      const r = await pg.evaluate(() => {
        const over = document.documentElement.scrollWidth - innerWidth;
        const small = [...document.querySelectorAll('button, a, input, select')].filter(e => { const r = e.getBoundingClientRect(); return r.width > 0 && r.height > 0 && r.height < 32 && getComputedStyle(e).visibility !== 'hidden'; }).length;
        const tabs = document.querySelector('.studio-modes'); const tabScroll = tabs ? tabs.scrollWidth > tabs.clientWidth : null;
        const fs = parseFloat(getComputedStyle(document.body).fontSize);
        return { overflowX: over, tapTargetsUnder32px: small, studioTabsScroll: tabScroll, bodyFont: fs };
      });
      console.log(w, p.padEnd(28), JSON.stringify(r));
      if (w === 360 && ['/tools/product-image-tools/', '/tools/invoice-generator/', '/'].includes(p)) await pg.screenshot({ path: `${S}/out/m360${p.replace(/\//g, '_')}.png`, fullPage: true });
    }
    await ctx.close();
  }
  // keyboard: tab through studio and report focus visibility
  const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } }); const pg = await ctx.newPage();
  await pg.goto(B + '/tools/image-resizer/', { waitUntil: 'networkidle' });
  const seen = [];
  for (let i = 0; i < 25; i++) {
    await pg.keyboard.press('Tab');
    seen.push(await pg.evaluate(() => { const e = document.activeElement; const cs = getComputedStyle(e); const visible = cs.outlineStyle !== 'none' && parseFloat(cs.outlineWidth) > 0 || cs.boxShadow !== 'none'; return (e.getAttribute('aria-label') || e.textContent || e.name || e.tagName).trim().slice(0, 22) + (visible ? '' : ' [NO FOCUS RING]'); }));
  }
  console.log('keyboard order:', seen.join(' > '));
  // reduced motion & lang
  console.log('html lang:', await pg.getAttribute('html', 'lang'));
  await b.close();
})();
