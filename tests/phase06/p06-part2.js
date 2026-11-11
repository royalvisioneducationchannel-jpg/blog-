const { chromium } = require('playwright'); const fs = require('fs');
const B = 'https://lightyellow-alligator-410734.hostingersite.com', S = process.argv[2];
const acc = JSON.parse(fs.readFileSync(S + '/p06-accounts.json')); const links = JSON.parse(process.argv[3]);
const log = (id, r) => console.log(id.padEnd(44), JSON.stringify(r));
const note = pg => pg.textContent('.stp-note').catch(() => null);
const wsShown = pg => pg.evaluate(() => (document.body.innerText.match(/Workspace #(\d+)/) || [])[1]);
(async () => {
  const b = await chromium.launch();
  const A = await b.newContext({ storageState: S + '/p06-state-a.json' }), Bc = await b.newContext({ storageState: S + '/p06-state-b.json' });
  const pa = await A.newPage(), pb = await Bc.newPage();
  const aLink = links[acc.a.email], bLink = links[acc.b.email];
  const forged = bLink.replace(/token=[^&]+/, 'token=' + new URL(aLink).searchParams.get('token'));
  await pb.goto(forged); log('V1 B uses A token with B uid', { note: await note(pb) });
  await pa.goto(aLink); log('V2 A verifies own email', { note: await note(pa), bannerGone: !(await pa.content()).includes('Please verify your email') });
  await pa.goto(aLink); log('V3 reuse same link', { note: await note(pa) });
  // REST isolation (cookie auth needs a wp_rest nonce; core endpoint returns it for signed-in users)
  await pa.goto(B + '/app/'); const nonce = await pa.evaluate(async () => (await fetch('/wp-admin/admin-ajax.php?action=rest-nonce')).text());
  const api = (p, n) => pa.evaluate(async ([p, n]) => { const r = await fetch('/wp-json/seller-tools/v1/' + p, { headers: n ? { 'X-WP-Nonce': n } : {} }); return r.status + ' ' + (await r.text()).slice(0, 90); }, [p, n]);
  log('I1 A /me', await api('me', nonce));
  log('I2 A reads own workspace ' + acc.a.ws, (await api('workspaces/' + acc.a.ws, nonce)).slice(0, 40));
  log('I3 A reads B workspace ' + acc.b.ws, await api('workspaces/' + acc.b.ws, nonce));
  const anon = await (await b.newContext()).newPage(); await anon.goto(B + '/');
  log('I4 anonymous reads workspace ' + acc.a.ws, await anon.evaluate(async id => { const r = await fetch('/wp-json/seller-tools/v1/workspaces/' + id); return r.status; }, acc.a.ws));
  await pa.goto(`${B}/app/?workspace=${acc.b.ws}&ws=${acc.b.ws}`); log('I5 A tampers ?workspace=B in URL', { shown: await wsShown(pa) });
  await pa.goto(`${B}/app/settings/?workspace_id=${acc.b.ws}`); log('I6 A settings with B id in URL', { shown: (await pa.innerText('.stp-main')).match(/Workspace #(\d+)/)?.[1] });
  await pb.goto(B + '/app/'); log('I7 B sees own workspace', { shown: await wsShown(pb), stillUnverified: (await pb.content()).includes('Please verify your email') });
  const r1 = await pa.goto(B + '/wp-admin/'); log('I8 customer opens /wp-admin/', { landed: new URL(pa.url()).pathname });
  await pa.goto(B + '/wp-admin/profile.php'); log('I9 customer opens profile.php', { landed: new URL(pa.url()).pathname });
  await pa.goto(B + '/app/settings/'); await pa.fill('#stp-st_name', 'Test A Renamed'); await pa.check('input[name=marketing]');
  await Promise.all([pa.waitForURL(/st_msg=saved/), pa.click('.stp-main form.stp-form button')]);
  log('S1 settings save', { note: await note(pa), name: await pa.inputValue('#stp-st_name'), consent: await pa.isChecked('input[name=marketing]') });
  await Promise.all([pa.waitForURL(/st_msg=signed_out/), pa.click('.stp-signout')]); log('L1 sign out', { note: await note(pa), signInForm: !!(await pa.$('input[name=st_action][value=login]')) });
  await pa.fill('#stp-email', acc.a.email); await pa.fill('#stp-password', acc.a.pass);
  await Promise.all([pa.waitForNavigation(), pa.click('form.stp-form button')]); log('L2 sign in again', { shown: await wsShown(pa), menu: (await pa.$$eval('[data-elementor-type=header] .elementor-nav-menu--main > ul > li > a', a => a.map(x => x.textContent.trim()))).slice(-1) });
  const lp = await (await b.newContext()).newPage();
  for (const e of [acc.b.email, 'nobody-' + Date.now() + '@example.com']) { await lp.goto(B + '/app/lost-password/'); await lp.fill('#stp-email', e); await Promise.all([lp.waitForURL(/st_msg=/), lp.click('form.stp-form button')]); log('P1 reset request ' + (e.startsWith('nobody') ? '(unknown email)' : '(B)'), { note: await note(lp) }); }
  const m = await b.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 2, storageState: S + '/p06-state-b.json' }); const pm = await m.newPage();
  await pm.goto(B + '/app/'); log('M1 mobile overview', { overflowX: await pm.evaluate(() => document.documentElement.scrollWidth - innerWidth) });
  await pm.screenshot({ path: S + '/p06-mobile.png', fullPage: true }); await pa.setViewportSize({ width: 1440, height: 900 }); await pa.goto(B + '/app/'); await pa.screenshot({ path: S + '/p06-desktop.png', fullPage: true });
  await anon.goto(B + '/app/'); await anon.screenshot({ path: S + '/p06-signin.png' });
  // Brute force last: it locks this IP for 15 minutes.
  const bf = await (await b.newContext()).newPage(); const notes = [];
  for (let i = 0; i < 6; i++) { await bf.goto(B + '/app/'); await bf.fill('#stp-email', acc.a.email); await bf.fill('#stp-password', 'wrong-password-' + i); await Promise.all([bf.waitForURL(/st_msg=/), bf.click('form.stp-form button')]); notes.push(await note(bf)); }
  log('L3 six wrong passwords', notes.map(n => n && n.slice(0, 25)));
  await b.close();
})();
