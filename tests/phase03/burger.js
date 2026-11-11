const { chromium } = require('playwright');
(async()=>{const b=await chromium.launch();const pg=await b.newPage({viewport:{width:390,height:844},deviceScaleFactor:2});
await pg.goto('https://lightyellow-alligator-410734.hostingersite.com/tools/?v='+Date.now(),{waitUntil:'networkidle'});
const t=pg.locator('[data-elementor-type="header"] .elementor-menu-toggle').first(); console.log('toggle visible:',await t.isVisible(), 'aria-label:',await t.getAttribute('aria-label'));
await t.click(); await pg.waitForTimeout(600);
const items=await pg.evaluate(()=>[...document.querySelectorAll('[data-elementor-type="header"] nav.elementor-nav-menu--dropdown a')].map(a=>{const r=a.getBoundingClientRect();return {t:a.textContent.trim(),L:Math.round(r.left),R:Math.round(r.right),vis:r.height>0&&getComputedStyle(a).visibility!=='hidden'}}));
console.log(JSON.stringify(items)); console.log('expanded:',await t.getAttribute('aria-expanded'),'overflowX:',await pg.evaluate(()=>document.documentElement.scrollWidth-innerWidth));
await pg.screenshot({path:process.argv[2]+'/p03-390-menu-open.png'}); await b.close();})();
