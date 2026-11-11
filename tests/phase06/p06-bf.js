const { chromium } = require('playwright'); const fs=require('fs');
const B='https://lightyellow-alligator-410734.hostingersite.com'; const acc=JSON.parse(fs.readFileSync(process.argv[2]+'/p06-accounts.json'));
(async()=>{const b=await chromium.launch(); const pg=await (await b.newContext()).newPage(); const out=[];
const tryLogin=async(email,pass)=>{await pg.goto(B+'/app/');await pg.fill('#stp-email',email);await pg.fill('#stp-password',pass);await Promise.all([pg.waitForNavigation(),pg.click('form.stp-form button')]);return (await pg.textContent('.stp-note').catch(()=>null))||('signed in, ws '+((await pg.innerText('body')).match(/Workspace #(\d+)/)||[])[1]);};
for(let i=0;i<6;i++) out.push((await tryLogin(acc.b.email,'wrong-'+i)).slice(0,22));
console.log('B six wrong:',JSON.stringify(out));
console.log('B correct password while locked:',await tryLogin(acc.b.email,acc.b.pass));
const p2=await (await b.newContext()).newPage(); await p2.goto(B+'/app/'); await p2.fill('#stp-email',acc.a.email); await p2.fill('#stp-password',acc.a.pass); await Promise.all([p2.waitForNavigation(),p2.click('form.stp-form button')]);
console.log('A unaffected:',((await p2.innerText('body')).match(/Workspace #(\d+)/)||[])[1]); await b.close();})();
