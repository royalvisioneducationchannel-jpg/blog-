const { chromium } = require('playwright'); const fs = require('fs');
const S = process.argv[2]; const T = 'https://lightyellow-alligator-410734.hostingersite.com/tools/';
const log = (id, r) => console.log(id.padEnd(42), JSON.stringify(r));
(async () => {
  const b = await chromium.launch(); const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } });
  let pg = await ctx.newPage(); await pg.goto(T + 'bulk-resizer/', { waitUntil: 'networkidle' });
  await pg.setInputFiles('input[name=photos]', fs.readdirSync(S + '/fixtures/big7').map(n => S + '/fixtures/big7/' + n));
  log('B4 7x16.5MB (>100MB combined)', { status: await pg.textContent('[data-studio-status]'), summary: await pg.textContent('[data-summary]') });

  // PROFIT
  pg = await ctx.newPage(); await pg.goto(T + 'profit-calculator/', { waitUntil: 'networkidle' });
  const P = async (v) => {
    for (const [k, x] of Object.entries(v)) {
      if (k === 'currency') await pg.selectOption('select[name=currency]', x); else await pg.fill(`input[name=${k}]`, x);
    }
    return pg.evaluate(() => ({ net: document.querySelector('[data-net]').textContent, margin: document.querySelector('[data-margin]').textContent, markup: document.querySelector('[data-markup]').textContent, total: document.querySelector('[data-expenses]').textContent }));
  };
  log('P0 default currency', await pg.inputValue('select[name=currency]'));
  log('P1 PKR 2500/1200/250/150/100/50', await P({ price: '2500', cost: '1200', shipping: '250', fees: '150', ads: '100', other: '50', currency: 'PKR' }));
  log('P2 loss 1000 vs cost 1300', await P({ price: '1000', cost: '1300', shipping: '', fees: '', ads: '', other: '' }));
  log('P3 decimals 19.99/7.333', await P({ price: '19.99', cost: '7.333', currency: 'USD' }));
  log('P4 negative cost -5', await P({ price: '100', cost: '-5' }));
  log('P5 empty price', await P({ price: '', cost: '10' }));
  log('P6 price 0 cost 10', await P({ price: '0', cost: '10' }));

  // INVOICE
  pg = await ctx.newPage(); await pg.goto(T + 'invoice-generator/', { waitUntil: 'networkidle' });
  await pg.fill('textarea[name=business]', 'Demo Seller\nLahore'); await pg.fill('textarea[name=client]', 'Demo Customer');
  await pg.fill('input[name=number]', 'INV-001'); await pg.selectOption('select[name=currency]', 'PKR');
  await pg.fill('input[name=item]', 'Demo mug'); await pg.fill('input[name=quantity]', '3'); await pg.fill('input[name=rate]', '33.33');
  await pg.click('[data-add]');
  const second = pg.locator('[data-items] fieldset').nth(1);
  await second.locator('input[name=item]').fill('Demo shipping'); await second.locator('input[name=rate]').fill('250');
  await pg.fill('input[name=tax]', '17');
  log('I1 totals 3x33.33 + 250, tax 17%', await pg.evaluate(() => [...document.querySelectorAll('[data-invoice=items] tr')].map(r => r.textContent).concat(['subtotal', 'tax', 'total'].map(k => document.querySelector(`[data-invoice=${k}]`).textContent))));
  log('I2 logo/discount fields', await pg.evaluate(() => ({ logo: !!document.querySelector('.workspace input[type=file]'), discount: !!document.querySelector('[name*=discount]') })));
  await pg.fill('input[name=due]', '2020-01-01'); await pg.click('form .actions button');
  log('I3 due before issue', await pg.textContent('[data-status]'));
  await pg.fill('input[name=due]', '');
  for (let i = 0; i < 25; i++) { if (await pg.$eval('[data-add]', e => e.disabled)) break; await pg.click('[data-add]'); }
  log('I4 max line items', { rows: await pg.locator('[data-items] fieldset').count(), addDisabled: await pg.$eval('[data-add]', e => e.disabled) });
  while (await pg.locator('[data-items] fieldset').count() > 2) { await pg.locator('[data-items] [data-remove]').last().click(); }
  await pg.emulateMedia({ media: 'print' });
  await pg.pdf({ path: S + '/out/invoice-print.pdf', format: 'A4' });
  await pg.screenshot({ path: S + '/out/invoice-print.png', fullPage: true });
  await pg.emulateMedia({ media: 'screen' });

  // UTM
  pg = await ctx.newPage(); await pg.goto(T + 'utm-builder/', { waitUntil: 'networkidle' });
  const U = async (v) => {
    for (const k of ['url', 'source', 'medium', 'campaign', 'term', 'content']) await pg.fill(`input[name=${k}]`, v[k] || '');
    await pg.click('form .actions button'); await pg.waitForTimeout(200);
    return { out: await pg.textContent('[data-url]'), status: await pg.textContent('[data-status]'), validity: await pg.$eval('input[name=url]', e => e.validationMessage) };
  };
  log('U1 valid + existing query', await U({ url: 'https://shop.example.com/p?id=7&utm_source=old', source: 'facebook', medium: 'paid social', campaign: 'eid sale' }));
  log('U2 invalid url text', await U({ url: 'not a url', source: 'a', medium: 'b', campaign: 'c' }));
  log('U3 javascript: url', await U({ url: 'javascript:alert(1)', source: 'a', medium: 'b', campaign: 'c' }));
  log('U4 credentials url', await U({ url: 'https://user:pw@example.com', source: 'a', medium: 'b', campaign: 'c' }));
  log('U5 urdu values', await U({ url: 'https://example.com', source: 'واٹس ایپ', medium: 'chat', campaign: 'عید' }));
  await b.close();
})();
