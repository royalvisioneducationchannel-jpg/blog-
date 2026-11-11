const { chromium } = require('playwright');
const OUT = process.argv[2]; const B = 'https://lightyellow-alligator-410734.hostingersite.com';
const paths = ['/', '/tools/', '/tools/image-resizer/', '/tools/invoice-generator/', '/tools/product-image-tools/'];
(async () => {
  const b = await chromium.launch();
  for (const [w, dpr] of [[1440, 1], [390, 2], [360, 1]]) {
    const ctx = await b.newContext({ viewport: { width: w, height: 900 }, deviceScaleFactor: dpr }); const pg = await ctx.newPage();
    const errs = []; pg.on('pageerror', e => errs.push(e.message));
    for (const p of paths) {
      await pg.goto(B + p + '?v=' + Date.now(), { waitUntil: 'networkidle' }); await pg.evaluate(() => document.fonts.ready);
      const r = await pg.evaluate(() => ({
        overflowX: document.documentElement.scrollWidth - innerWidth,
        elHeader: !!document.querySelector('[data-elementor-type="header"]'), elFooter: !!document.querySelector('[data-elementor-type="footer"]'),
        themeHeader: !!document.querySelector('header.site-head'), poppins: document.fonts.check('600 16px Poppins'),
        googleFonts: [...document.querySelectorAll('link[href*="fonts.googleapis"]')].length,
        h1Font: document.querySelector('h1') ? getComputedStyle(document.querySelector('h1')).fontFamily.split(',')[0] : null,
        studioOk: document.querySelector('[data-studio] form') ? !document.querySelector('[data-studio] form').inert : null,
      }));
      console.log(w, p.padEnd(28), JSON.stringify(r), errs.splice(0).join('|'));
      if ((w === 1440 || w === 390) && ['/', '/tools/image-resizer/'].includes(p)) await pg.screenshot({ path: `${OUT}/p03-${w}${p.replace(/\//g, '_')}.png`, fullPage: w === 1440 && p === '/' });
    }
    await ctx.close();
  }
  await b.close();
})();
