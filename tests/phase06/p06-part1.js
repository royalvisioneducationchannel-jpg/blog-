const { chromium } = require('playwright'); const fs = require('fs'); const crypto = require('crypto');
const B = 'https://lightyellow-alligator-410734.hostingersite.com', S = process.argv[2];
const log = (id, r) => console.log(id.padEnd(40), JSON.stringify(r));
(async () => {
  const b = await chromium.launch(); const stamp = Date.now();
  // Anonymous view and headers
  const anon = await b.newContext(); const pa = await anon.newPage();
  const res = await pa.goto(B + '/app/', { waitUntil: 'networkidle' }); const h = res.headers();
  log('A0 anonymous /app/', { status: res.status(), signInForm: !!(await pa.$('input[name=st_action][value=login]')), cache: h['cache-control'], robots: h['x-robots-tag'], menu: (await pa.$$eval('[data-elementor-type=header] .elementor-nav-menu--main a', a => a.map(x => x.textContent.trim()))).slice(-1) });
  // Spam controls: too-fast submit and honeypot
  await pa.goto(B + '/app/register/'); await pa.fill('#stp-email', `fast-${stamp}@example.com`); await pa.fill('#stp-password', 'Test-pass-12345'); await pa.click('form.stp-form button');
  log('A1 register submitted in <3s', { url: new URL(pa.url()).search, note: await pa.textContent('.stp-note').catch(() => null) });
  await pa.goto(B + '/app/register/'); await pa.fill('#stp-email', `bot-${stamp}@example.com`); await pa.fill('#stp-password', 'Test-pass-12345'); await pa.$eval('input[name=website]', e => e.value = 'http://spam.example'); await pa.waitForTimeout(3500); await pa.click('form.stp-form button');
  log('A2 honeypot filled', { note: await pa.textContent('.stp-note').catch(() => null) });
  await pa.goto(B + '/app/register/'); await pa.fill('#stp-email', `short-${stamp}@example.com`); await pa.fill('#stp-password', 'short'); await pa.waitForTimeout(3500); await pa.click('form.stp-form button');
  log('A3 weak password', { note: await pa.textContent('.stp-note').catch(() => null) });
  // Two real test accounts
  const accounts = {};
  for (const who of ['a', 'b']) {
    const ctx = await b.newContext(); const pg = await ctx.newPage();
    const email = `st-test-${who}-${stamp}@example.com`, pass = crypto.randomBytes(12).toString('base64url');
    await pg.goto(B + '/app/register/'); await pg.fill('#stp-st_name', `Test ${who.toUpperCase()}`); await pg.fill('#stp-email', email); await pg.fill('#stp-password', pass);
    await pg.waitForTimeout(3500); await Promise.all([pg.waitForURL(/st_msg=/), pg.click('form.stp-form button')]); await pg.waitForLoadState('networkidle');
    const ws = await pg.evaluate(() => (document.body.innerText.match(/Workspace #(\d+)/) || [])[1]);
    log(`R-${who} register`, { msg: await pg.textContent('.stp-note').catch(() => null), workspace: ws, verifyBanner: (await pg.content()).includes('Please verify your email'), adminBar: !!(await pg.$('#wpadminbar')) });
    accounts[who] = { email, pass, ws: Number(ws) };
    await ctx.storageState({ path: `${S}/p06-state-${who}.json` }); await ctx.close();
  }
  fs.writeFileSync(`${S}/p06-accounts.json`, JSON.stringify(accounts));
  await b.close();
})();
