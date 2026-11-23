import { useEffect, useRef, useState } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { projects, profile, type Project } from '../content';
import { useMediaQuery } from '../motion';
import { BrowserFrame, Card, PhoneFrame } from './Frames';

gsap.registerPlugin(ScrollTrigger);

function Media({ p, eager = false }: { p: Project; eager?: boolean }) {
  if (p.creative) {
    return (
      <div className="media media--creative">
        <Card shot={p.creative[0]} className="m-a" />
        {p.creative[1] && <Card shot={p.creative[1]} className="m-b" />}
      </div>
    );
  }
  return (
    <div className={`media ${p.mobile ? 'media--duo' : 'media--solo'}`}>
      {p.desktop && <BrowserFrame shot={p.desktop} url={p.live} eager={eager} className="m-desk" />}
      {p.mobile && <PhoneFrame shot={p.mobile} className="m-phone" />}
    </div>
  );
}

export default function Work({ onOpen }: { onOpen: (id: string) => void }) {
  const desktop = useMediaQuery('(min-width: 960px)');
  const [active, setActive] = useState(0);
  const steps = useRef<(HTMLElement | null)[]>([]);

  useEffect(() => {
    if (!desktop) return;
    const triggers = steps.current.map((el, i) =>
      ScrollTrigger.create({
        trigger: el,
        start: 'top 55%',
        end: 'bottom 55%',
        onToggle: (self) => self.isActive && setActive(i),
      }),
    );
    return () => triggers.forEach((t) => t.kill());
  }, [desktop]);

  const goTo = (i: number) => {
    const el = steps.current[i];
    if (!el) return;
    const smooth = !document.documentElement.classList.contains('motion-off');
    window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - window.innerHeight * 0.2, behavior: smooth ? 'smooth' : 'auto' });
  };

  const pad = (n: number) => String(n).padStart(2, '0');

  return (
    <section className="work section--dark" id="work" aria-labelledby="work-title">
      <div className="container">
        <header className="section-head">
          <p className="kicker">Selected work</p>
          <h2 id="work-title" className="h2">
            Real projects, <span className="muted">live and in use.</span>
          </h2>
          <p className="section-lead">
            Each project shows the brief, my role, what I delivered and the tools used. Visit the live sites or open a case study for details.
          </p>
        </header>

        <div className="work__grid">
          {desktop && (
            <div className="work__stage" aria-hidden="true">
              <div className="work__stage-inner">
                {projects.map((p, i) => (
                  <div
                    key={p.id}
                    className={`stage-item stage-item--${i + 1} ${i === active ? 'is-active' : i < active ? 'is-before' : 'is-after'}`}
                  >
                    <Media p={p} eager={i === 0} />
                  </div>
                ))}
              </div>
              <div className="work__progress">
                <span className="work__count">
                  {pad(active + 1)} <span>/ {pad(projects.length)}</span>
                </span>
                <span className="work__bar">
                  <span style={{ transform: `scaleX(${(active + 1) / projects.length})` }} />
                </span>
              </div>
            </div>
          )}

          <div className="work__steps">
            {desktop && (
              <nav className="work__nav" aria-label="Jump to project">
                {projects.map((p, i) => (
                  <button key={p.id} type="button" className={i === active ? 'is-active' : ''} aria-current={i === active ? 'true' : undefined} onClick={() => goTo(i)}>
                    <span>{pad(i + 1)}</span> {p.title}
                  </button>
                ))}
              </nav>
            )}
            {projects.map((p, i) => (
              <article key={p.id} className={`step ${i === active ? 'is-active' : ''}`} ref={(el) => (steps.current[i] = el)} aria-labelledby={`${p.id}-t`}>
                {!desktop && (
                  <div className="step__media">
                    <Media p={p} />
                  </div>
                )}
                <p className="step__index">
                  {pad(i + 1)} — {p.category}
                </p>
                <h3 id={`${p.id}-t`} className="h3">
                  {p.title}
                </h3>
                <p className="step__brief">{p.brief}</p>
                <dl className="step__meta">
                  <div>
                    <dt>My role</dt>
                    <dd>{p.role}</dd>
                  </div>
                  <div>
                    <dt>Delivered</dt>
                    <dd>
                      <ul>
                        {p.delivered.slice(0, 4).map((d) => (
                          <li key={d}>{d}</li>
                        ))}
                      </ul>
                    </dd>
                  </div>
                  <div>
                    <dt>Tools</dt>
                    <dd className="tags">
                      {p.tools.map((t) => (
                        <span key={t}>{t}</span>
                      ))}
                    </dd>
                  </div>
                  <div>
                    <dt>{p.outcomeVerified ? 'Verified result' : 'Outcome'}</dt>
                    <dd>{p.outcome}</dd>
                  </div>
                </dl>
                <div className="step__ctas">
                  <button type="button" className="btn btn--light" onClick={() => onOpen(p.id)}>
                    View Case Study
                  </button>
                  {p.live && (
                    <a className="btn btn--ghost" href={p.live} target="_blank" rel="noopener noreferrer">
                      Visit Live Website ↗
                    </a>
                  )}
                </div>
              </article>
            ))}
            <aside className="trust-inline">
              <span>Live projects you can visit</span>
              <span>Ad results from Meta Ads Manager</span>
              <a href={profile.links.upwork} target="_blank" rel="noopener noreferrer">
                Hire securely via Upwork ↗
              </a>
            </aside>
          </div>
        </div>
      </div>
    </section>
  );
}
