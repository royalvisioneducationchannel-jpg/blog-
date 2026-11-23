import { useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { projects } from '../content';
import { useMotion } from '../motion';

gsap.registerPlugin(ScrollTrigger);

export default function Work({ onOpen }: { onOpen: (id: string) => void }) {
  const { motion } = useMotion();
  const root = useRef<HTMLElement>(null);

  useEffect(() => {
    if (!motion) return;
    const ctx = gsap.context(() => {
      const cards = gsap.utils.toArray<HTMLElement>('.pcard');
      cards.forEach((card, i) => {
        if (i === cards.length - 1) return;
        gsap.to(card.querySelector('.pcard__inner'), {
          scale: 0.9,
          opacity: 0.35,
          ease: 'none',
          scrollTrigger: { trigger: cards[i + 1], start: 'top bottom', end: 'top 15%', scrub: true },
        });
      });
      gsap.utils.toArray<HTMLElement>('.pcard__shot').forEach((el) => {
        gsap.fromTo(el, { yPercent: 6 }, { yPercent: -6, ease: 'none', scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: true } });
      });
    }, root);
    return () => ctx.revert();
  }, [motion]);

  return (
    <section className="work3" id="work" ref={root} aria-labelledby="work-title">
      <div className="wrap sec-head">
        <p className="eyebrow">Selected work</p>
        <h2 id="work-title" className="h2">
          Projects that are <em>live</em> and working.
        </h2>
        <p className="sec-lead">Real websites and campaigns. Open any project for the brief, my role and what was delivered.</p>
      </div>

      <div className="wrap pstack">
        {projects.map((p, i) => {
          const shot = p.desktop ?? p.creative![0];
          return (
            <article key={p.id} className="pcard" style={{ ['--i' as string]: i }} aria-labelledby={`pc-${p.id}`}>
              <div className="pcard__inner">
                <div className="pcard__text">
                  <p className="pcard__idx">
                    {String(i + 1).padStart(2, '0')} <span>/ {String(projects.length).padStart(2, '0')}</span>
                  </p>
                  <p className="pcard__cat">{p.category}</p>
                  <h3 id={`pc-${p.id}`} className="pcard__title">
                    {p.title}
                  </h3>
                  <p className="pcard__brief">{p.brief}</p>
                  <ul className="tags">
                    {p.tags.map((t) => (
                      <li key={t}>{t}</li>
                    ))}
                  </ul>
                  <div className="pcard__ctas">
                    <button type="button" className="btn btn--light btn--sm" onClick={() => onOpen(p.id)}>
                      View case study
                    </button>
                    {p.live && (
                      <a className="btn btn--ghost btn--sm" href={p.live} target="_blank" rel="noopener noreferrer">
                        Live site ↗
                      </a>
                    )}
                  </div>
                </div>
                <button type="button" className="pcard__media" data-cursor="view" onClick={() => onOpen(p.id)} aria-label={`Open ${p.title} case study`}>
                  <div className="pcard__shot">
                    <div className="frame">
                      <div className="frame__bar" aria-hidden="true">
                        <i />
                        <i />
                        <i />
                        {p.live && <span>{p.live.replace(/^https?:\/\//, '').replace(/\/$/, '')}</span>}
                      </div>
                      <img src={shot.src} width={shot.w} height={shot.h} alt={shot.alt} loading={i === 0 ? 'eager' : 'lazy'} decoding="async" />
                    </div>
                    {p.mobile && (
                      <div className="phone2">
                        <img src={p.mobile.src} width={p.mobile.w} height={p.mobile.h} alt={p.mobile.alt} loading="lazy" decoding="async" />
                      </div>
                    )}
                  </div>
                </button>
              </div>
            </article>
          );
        })}
      </div>
    </section>
  );
}
