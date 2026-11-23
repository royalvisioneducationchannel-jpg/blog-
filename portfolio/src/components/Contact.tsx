import { useEffect, useState, type FormEvent } from 'react';
import { budgets, profile, projectTypes, projects } from '../content';
import { useInquiry } from '../motion';

type Status = { kind: 'idle' | 'sending' | 'success' | 'error' | 'info'; msg?: string };

const thumbs = [projects[0].desktop!, projects[2].desktop!, projects[3].creative![0]];

export default function Contact() {
  const { inquiry } = useInquiry();
  const [form, setForm] = useState({ name: '', email: '', projectType: '', budget: '', message: '', pkg: '' });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [status, setStatus] = useState<Status>({ kind: 'idle' });

  useEffect(() => {
    if (inquiry.projectType || inquiry.pkg) {
      setForm((f) => ({ ...f, projectType: inquiry.projectType ?? f.projectType, pkg: inquiry.pkg ?? '' }));
    }
  }, [inquiry]);

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
      document.querySelector<HTMLElement>('.field--error input, .field--error select, .field--error textarea')?.focus();
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
        setForm({ name: '', email: '', projectType: '', budget: '', message: '', pkg: '' });
      } catch {
        setStatus({ kind: 'error', msg: 'Sorry, the message could not be sent. Please try again or contact me on Upwork.' });
      }
      return;
    }
    if (profile.links.email) {
      const href = `mailto:${profile.links.email}?subject=${encodeURIComponent(`Project inquiry: ${form.projectType}`)}&body=${encodeURIComponent(brief())}`;
      window.location.href = href;
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
      msg: copied
        ? 'Your brief is copied. Paste it in a message to me on Upwork — the link below opens my profile.'
        : 'Please send this brief to me on Upwork — the link below opens my profile.',
    });
  };

  const field = (k: string) => `field ${errors[k] ? 'field--error' : ''}`;

  return (
    <section className="contact section--dark" id="contact" aria-labelledby="contact-title">
      <div className="contact__thumbs" aria-hidden="true">
        {thumbs.map((t, i) => (
          <img key={t.src} src={t.src} alt="" width={t.w} height={t.h} loading="lazy" className={`ct ct--${i + 1} float-soft`} />
        ))}
      </div>
      <div className="container contact__grid">
        <div className="contact__copy">
          <p className="kicker">Contact</p>
          <h2 id="contact-title" className="h2 contact__title">
            Let’s build something <span className="accent">worth showing.</span>
          </h2>
          <p className="section-lead">
            Tell me about your business and what you need. I’ll reply with questions or a clear plan and price — no obligation.
          </p>
          <ul className="contact__routes">
            <li>
              <a href={profile.links.upwork} target="_blank" rel="noopener noreferrer">
                <strong>Upwork</strong>
                <span>Hire me with Upwork payment protection ↗</span>
              </a>
            </li>
            {profile.links.email && (
              <li>
                <a href={`mailto:${profile.links.email}`}>
                  <strong>Email</strong>
                  <span>{profile.links.email}</span>
                </a>
              </li>
            )}
            {profile.links.linkedin && (
              <li>
                <a href={profile.links.linkedin} target="_blank" rel="noopener noreferrer">
                  <strong>LinkedIn</strong>
                  <span>Connect with me ↗</span>
                </a>
              </li>
            )}
            {profile.links.whatsapp && (
              <li>
                <a href={profile.links.whatsapp} target="_blank" rel="noopener noreferrer">
                  <strong>WhatsApp</strong>
                  <span>Send a message ↗</span>
                </a>
              </li>
            )}
          </ul>
        </div>

        <form className="form" onSubmit={onSubmit} noValidate aria-describedby="form-status">
          {form.pkg && (
            <p className="form__pkg">
              Selected: <strong>{form.pkg}</strong>
              <button type="button" onClick={() => setForm((f) => ({ ...f, pkg: '' }))} aria-label="Remove selected package">
                ✕
              </button>
            </p>
          )}
          <div className="form__row">
            <div className={field('name')}>
              <label htmlFor="f-name">Name</label>
              <input id="f-name" name="name" autoComplete="name" value={form.name} onChange={set('name')} aria-invalid={!!errors.name} aria-describedby={errors.name ? 'e-name' : undefined} />
              {errors.name && <p id="e-name" className="field__err">{errors.name}</p>}
            </div>
            <div className={field('email')}>
              <label htmlFor="f-email">Email</label>
              <input id="f-email" name="email" type="email" autoComplete="email" value={form.email} onChange={set('email')} aria-invalid={!!errors.email} aria-describedby={errors.email ? 'e-email' : undefined} />
              {errors.email && <p id="e-email" className="field__err">{errors.email}</p>}
            </div>
          </div>
          <div className="form__row">
            <div className={field('projectType')}>
              <label htmlFor="f-type">Project type</label>
              <select id="f-type" name="projectType" value={form.projectType} onChange={set('projectType')} aria-invalid={!!errors.projectType} aria-describedby={errors.projectType ? 'e-type' : undefined}>
                <option value="">Choose…</option>
                {projectTypes.map((t) => (
                  <option key={t}>{t}</option>
                ))}
              </select>
              {errors.projectType && <p id="e-type" className="field__err">{errors.projectType}</p>}
            </div>
            <div className="field">
              <label htmlFor="f-budget">
                Budget <span className="opt">(optional)</span>
              </label>
              <select id="f-budget" name="budget" value={form.budget} onChange={set('budget')}>
                <option value="">Choose…</option>
                {budgets.map((b) => (
                  <option key={b}>{b}</option>
                ))}
              </select>
            </div>
          </div>
          <div className={field('message')}>
            <label htmlFor="f-msg">Project description</label>
            <textarea id="f-msg" name="message" rows={5} value={form.message} onChange={set('message')} aria-invalid={!!errors.message} aria-describedby={errors.message ? 'e-msg' : undefined} placeholder="What do you need, and is there a deadline?" />
            {errors.message && <p id="e-msg" className="field__err">{errors.message}</p>}
          </div>
          <button type="submit" className="btn btn--primary btn--block" disabled={status.kind === 'sending'}>
            {status.kind === 'sending' ? 'Sending…' : profile.formEndpoint ? 'Send Inquiry' : profile.links.email ? 'Send via Email' : 'Prepare My Brief'}
          </button>
          <div id="form-status" className={`form__status form__status--${status.kind}`} role="status" aria-live="polite">
            {status.msg}
            {status.kind === 'info' && !profile.links.email && (
              <a className="link-btn" href={profile.links.upwork} target="_blank" rel="noopener noreferrer">
                {' '}
                Open my Upwork profile ↗
              </a>
            )}
          </div>
        </form>
      </div>
    </section>
  );
}
