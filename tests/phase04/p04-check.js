const { chromium } = require('playwright');
const B = 'https://lightyellow-alligator-410734.hostingersite.com';
const pages = ['/', '/tools/', '/pricing/', '/pricing/image-pro/', '/pricing/seller-desk/', '/pricing/seller-suite/', '/how-it-works/', '/guides/', '/help/', '/contact/', '/tools/image-resizer/', '/tools/image-compressor/', '/tools/square-maker/', '/tools/bulk-resizer/', '/tools/watermark/', '/tools/background-remover/', '/tools/product-image-tools/', '/tools/profit-calculator/', '/tools/invoice-generator/', '/tools/utm-builder/'];
(async () => {
  const b = await chromium.launch(); const links = new Set(); const bad = [];
  const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } }); const pg = await ctx.newPage();
  const errs = []; pg.on('pageerror', e => errs.push(e.message));
  for (const p of pages) {
    const res = await pg.goto(B + p, { waitUntil: 'networkidle' });
    const r = await pg.evaluate(() => ({
      h1: document.querySelectorAll('h1').length, dir: !!document.querySelector('[data-st-dir]'), pricing: !!document.querySelector('[data-st-pricing]'),
      tool: !!document.querySelector('[data-studio], [data-tool]'), inert: !!document.querySelector('form[inert]'),
      deadHash: [...document.querySelectorAll('a[href="#"], a[href=""]')].filter(a => a.offsetParent).map(a => a.textContent.trim().slice(0, 30)),
      links: [...document.querySelectorAll('a[href]')].map(a => a.href).filter(h => h.startsWith(location.origin)),
      title: document.title,
    }));
    r.links.forEach(l => links.add(l.split('#')[0]));
    console.log(String(res.status()), p.padEnd(28), `h1=${r.h1}`, r.dir ? 'DIR' : '', r.pricing ? 'PRICING' : '', r.tool ? 'TOOL' : '', r.inert ? 'INERT!' : '', r.deadHash.length ? 'DEAD:' + r.deadHash.join('|') : '', errs.splice(0).join('|'));
  }
  for (const l of links) { const res = await pg.goto(l, { waitUntil: "domcontentloaded" }); if (!res || res.status() >= 400) bad.push((res ? res.status() : "ERR") + " " + l); }
  console.log(`internal links checked: ${links.size}, broken: ${bad.length}`, bad.join(', '));
  // Directory filters
  await pg.goto(B + '/tools/', { waitUntil: 'networkidle' });
  const vis = () => pg.$$eval('[data-st-item]', l => l.filter(e => !e.hidden).length);
  const out = { all: await vis() };
  await pg.click('[data-st-task="Money"]'); out.money = await vis();
  await pg.click('[data-st-task="all"]'); await pg.click('[data-st-access="paid"]'); out.paid = await vis();
  await pg.click('[data-st-access="all"]'); await pg.fill('[data-st-search]', 'invoice'); out.searchInvoice = await vis();
  await pg.fill('[data-st-search]', 'zzzz'); out.emptyShown = await pg.$eval('[data-st-empty]', e => !e.hidden);
  await pg.click('[data-st-reset]'); out.afterReset = await vis(); out.status = await pg.textContent('[data-st-status]');
  console.log('directory:', JSON.stringify(out));
  await pg.goto(B + '/pricing/', { waitUntil: 'networkidle' });
  const before = await pg.$$eval('[data-price-month]:not([hidden])', e => e.length);
  await pg.click('[data-st-interval="year"]');
  const after = await pg.evaluate(() => ({ yearVisible: document.querySelectorAll('[data-price-year]:not([hidden])').length, monthVisible: document.querySelectorAll('[data-price-month]:not([hidden])').length, texts: [...document.querySelectorAll('[data-price-year]:not([hidden])')].map(e => e.textContent.trim()) }));
  console.log('pricing toggle:', JSON.stringify({ monthBefore: before, ...after }), 'disabled buys:', await pg.$$eval('button.st-btn--disabled[disabled]', e => e.length));
  await ctx.close();
  for (const w of [360, 390, 768, 1024]) {
    const c2 = await b.newContext({ viewport: { width: w, height: 800 } }); const p2 = await c2.newPage(); const over = [];
    for (const p of pages) { await p2.goto(B + p, { waitUntil: 'networkidle' }); const o = await p2.evaluate(() => document.documentElement.scrollWidth - innerWidth); if (o > 0) over.push(p + ':' + o); }
    console.log(w, 'overflow pages:', over.length ? over.join(' ') : 'none'); await c2.close();
  }
  await b.close();
})();
