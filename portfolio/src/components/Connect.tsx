import { useEffect, useRef, useState, type FormEvent } from 'react';
import { budgets, profile, projectTypes } from '../content';
import { useInquiry } from '../motion';

type Status = { kind: 'idle' | 'sending' | 'success' | 'error' | 'info'; msg?: string };
const empty = { name: '', email: '', projectType: '', budget: '', message: '', pkg: '' };

function ContactDialog() {
  const { inquiry, open, close } = useInquiry();
  const ref = useRef<HTMLDialogElement>(null);
  const [form, setForm] = useState(empty);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [status, setStatus] = useState<Status>({ kind: 'idle' });

  useEffect(() => {
    const d = ref.current;
    if (!d) return;
    if (open && !d.open) {
      setForm((f) => ({ ...f, projectType: inquiry.projectType ?? f.projectType, pkg: inquiry.pkg ?? '' }));
      setStatus({ kind: 'idle' });
      d.showModal();
    }
    if (!open && d.open) d.close();
  }, [open, inquiry]);

  const set = (k: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) =>
    setForm((f) => ({ ...f, [k]: e.target.value }));

  const validate = () => {
    const e: Record<string, string> = {};
    if (!form.name.trim()) e.name = 'Please enter your name.';
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) e.email = 'Please enter a valid email address.';
    if (!form.projectType) e.projectType = 'Please choose a project type.';
    if (form.message.trim().length < 10) e.message = 'Please describe your project in a few words.';
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const brief = () => {
    const head = [`Name: ${form.name}`, `Email: ${form.email}`, `Project type: ${form.projectType}`];
    if (form.pkg) head.push(`Package: ${form.pkg}`);
    if (form.budget) head.push(`Budget: ${form.budget}`);
    return `${head.join('\n')}\n\n${form.message}`;
  };

  const onSubmit = async (ev: FormEvent) => {
    ev.preventDefault();
    if (!validate()) {
      ref.current?.querySelector<HTMLElement>('[aria-invalid="true"]')?.focus();
      return;
    }
    if (profile.formEndpoint) {
      setStatus({ kind: 'sending' });
      try {
        const res = await fetch(profile.formEndpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify(form),
        });
        if (!res.ok) throw new Error(String(res.status));
        setStatus({ kind: 'success', msg: 'Thank you! Your message was sent. I’ll reply as soon as possible.' });
        setForm(empty);
      } catch {
        setStatus({ kind: 'error', msg: 'Sorry, the message could not be sent. Please try again or contact me on Upwork.' });
      }
      return;
    }
    if (profile.links.email) {
      window.location.href = `mailto:${profile.links.email}?subject=${encodeURIComponent(`Project inquiry: ${form.projectType}`)}&body=${encodeURIComponent(brief())}`;
      setStatus({ kind: 'info', msg: `Your email app should open with this message ready to send. If it doesn’t, email ${profile.links.email} or message me on Upwork.` });
      return;
    }
    let copied = false;
    try {
      await navigator.clipboard.writeText(brief());
      copied = true;
    } catch {
      /* clipboard unavailable */
    }
    setStatus({
      kind: 'info',
      msg: copied ? 'Your brief is copied. Paste it in a message to me on Upwork.' : 'Please send this brief to me on Upwork.',
    });
  };

  const err = (k: string) => (errors[k] ? { 'aria-invalid': true, 'aria-describedby': `e-${k}` } : {});

  return (
    <dialog ref={ref} className="cdlg" aria-labelledby="cdlg-title" onClose={close} onClick={(e) => e.target === ref.current && close()}>
      <form className="cform" onSubmit={onSubmit} noValidate>
        <button type="button" className="x" onClick={close} aria-label="Close contact form">
          ✕
        </button>
        <h2 id="cdlg-title">Let’s start a project together</h2>
        {form.pkg && (
          <p className="cform__pkg">
            Selected: <strong>{form.pkg}</strong>
            <button type="button" onClick={() => setForm((f) => ({ ...f, pkg: '' }))} aria-label="Remove selected package">
              ✕
            </button>
          </p>
        )}
        <label htmlFor="f-name">Name</label>
        <input id="f-name" autoComplete="name" placeholder="Your name" value={form.name} onChange={set('name')} {...err('name')} />
        {errors.name && <p id="e-name" className="ferr">{errors.name}</p>}
        <label htmlFor="f-email">Email</label>
        <input id="f-email" type="email" autoComplete="email" placeholder="Your email" value={form.email} onChange={set('email')} {...err('email')} />
        {errors.email && <p id="e-email" className="ferr">{errors.email}</p>}
        <div className="cform__row">
          <div>
            <label htmlFor="f-type">What do you need?</label>
            <select id="f-type" value={form.projectType} onChange={set('projectType')} {...err('projectType')}>
              <option value="">Choose…</option>
              {projectTypes.map((t) => (
                <option key={t}>{t}</option>
              ))}
            </select>
            {errors.projectType && <p id="e-projectType" className="ferr">{errors.projectType}</p>}
          </div>
          <div>
            <label htmlFor="f-budget">
              Budget <span className="muted">(optional)</span>
            </label>
            <select id="f-budget" value={form.budget} onChange={set('budget')}>
              <option value="">Choose…</option>
              {budgets.map((b) => (
                <option key={b}>{b}</option>
              ))}
            </select>
          </div>
        </div>
        <label htmlFor="f-msg">Tell me about your project</label>
        <textarea id="f-msg" rows={4} placeholder="What do you need, and is there a deadline?" value={form.message} onChange={set('message')} {...err('message')} />
        {errors.message && <p id="e-message" className="ferr">{errors.message}</p>}
        <button type="submit" className="pillbtn pillbtn--accent" disabled={status.kind === 'sending'}>
          {status.kind === 'sending' ? 'Sending…' : profile.formEndpoint ? 'Send' : profile.links.email ? 'Send via email' : 'Prepare my brief'}
        </button>
        <div className={`cform__status is-${status.kind}`} role="status" aria-live="polite">
          {status.msg}{' '}
          {status.kind === 'info' && !profile.links.email && (
            <a href={profile.links.upwork} target="_blank" rel="noopener noreferrer">
              Open my Upwork profile ↗
            </a>
          )}
        </div>
      </form>
    </dialog>
  );
}

export default function Connect() {
  const { choose } = useInquiry();
  const links = [
    ['Upwork', profile.links.upwork],
    ['LinkedIn', profile.links.linkedin],
    ['WhatsApp', profile.links.whatsapp],
    ['Email', profile.links.email && `mailto:${profile.links.email}`],
  ].filter(([, h]) => h) as [string, string][];

  return (
    <footer className="cta3" id="contact" aria-labelledby="cta-title">
      <div className="cta3__glow" aria-hidden="true" />
      <div className="wrap cta3__inner">
        <p className="eyebrow">Have a project in mind?</p>
        <h2 id="cta-title" className="cta3__title">
          Let’s build something <em>worth showing.</em>
        </h2>
        <p className="cta3__lead">Tell me what you need. I’ll reply with questions or a clear plan and price, with no obligation.</p>
        <button type="button" className="orb3" data-magnetic onClick={() => choose()}>
          <span>Start a project</span>
        </button>
        <div className="cta3__bottom">
          <p>
            © {new Date().getFullYear()} {profile.name} · {profile.location}
          </p>
          <ul>
            {links.map(([l, h]) => (
              <li key={l}>
                <a href={h} target={h.startsWith('mailto') ? undefined : '_blank'} rel="noopener noreferrer">
                  {l} ↗
                </a>
              </li>
            ))}
            <li>
              <a href="#top">Back to top ↑</a>
            </li>
          </ul>
        </div>
      </div>
      <ContactDialog />
    </footer>
  );
}
