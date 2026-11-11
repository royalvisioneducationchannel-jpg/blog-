const { chromium } = require('playwright');
// Verifies header items are fully inside the viewport (overflow-x:clip would otherwise hide problems).
(async () => {
  const b = await chromium.launch();
  for (const w of [1440, 1024, 768, 390, 360]) {
    const pg = await b.newPage({ viewport: { width: w, height: 800 } });
    await pg.goto('https://lightyellow-alligator-410734.hostingersite.com/?v=' + Date.now(), { waitUntil: 'networkidle' });
    const r = await pg.evaluate(() => {
      const h = document.querySelector('[data-elementor-type="header"]'); const W = innerWidth;
      const vis = [...h.querySelectorAll('a, .elementor-menu-toggle, img')].filter(e => { const s = getComputedStyle(e); const r = e.getBoundingClientRect(); return r.width > 0 && r.height > 0 && s.visibility !== 'hidden' && !e.closest('.elementor-nav-menu--dropdown'); });
      const out = vis.filter(e => { const r = e.getBoundingClientRect(); return r.left < 0 || r.right > W + 0.5; }).map(e => (e.textContent || e.className).trim().slice(0, 20));
      const rows = new Set(vis.filter(e => e.matches('.elementor-nav-menu--main a')).map(e => Math.round(e.getBoundingClientRect().top)));
      return { headerH: Math.round(h.getBoundingClientRect().height), outside: out, menuRows: rows.size, burger: !!vis.find(e => e.matches('.elementor-menu-toggle')) };
    });
    console.log(w, JSON.stringify(r));
    if (w === 1440) await pg.screenshot({ path: process.argv[2] + '/hdr-1440.png', clip: { x: 0, y: 0, width: 1440, height: 80 } });
    await pg.close();
  }
  await b.close();
})();
