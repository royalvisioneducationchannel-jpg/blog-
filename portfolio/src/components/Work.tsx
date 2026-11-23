import { useEffect, useRef, useState } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { projects } from '../content';

gsap.registerPlugin(ScrollTrigger);

function Marquee({ text }: { text: string }) {
  const item = `${text} `;
  return (
    <div className="mq" aria-hidden="true">
      <div className="mq__track">
        {Array.from({ length: 6 }).map((_, i) => (
          <span key={i}>{item}</span>
        ))}
      </div>
    </div>
  );
}

export default function Work({ onOpen }: { onOpen: (id: string) => void }) {
  const [active, setActive] = useState(0);
  const runway = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const st = ScrollTrigger.create({
      trigger: runway.current,
      start: 'top top',
      end: 'bottom bottom',
      onUpdate: (self) => setActive(Math.min(projects.length - 1, Math.floor(self.progress * projects.length))),
    });
    return () => st.kill();
  }, []);

  return (
    <section className="work2 light" id="work" aria-labelledby="work-title">
      <h2 id="work-title" className="display center">
        Selected Works
      </h2>

      <div className="work2__runway" ref={runway} style={{ height: `${projects.length * 100 + 40}svh` }}>
        <div className="work2__sticky">
          {projects.map((p, i) => (
            <div key={p.id} className={`work2__name ${i === active ? 'is-on' : ''}`}>
              <Marquee text={p.title.toUpperCase()} />
            </div>
          ))}

          {projects.map((p, i) => {
            const shot = p.desktop ?? p.creative![0];
            const state = i === active ? 'is-active' : i < active ? 'is-before' : 'is-after';
            return (
              <article key={p.id} className={`wcard ${state}`} aria-hidden={i !== active} aria-labelledby={`wc-${p.id}`}>
                <button type="button" className="wcard__open" onClick={() => onOpen(p.id)} tabIndex={i === active ? 0 : -1}>
                  <img src={shot.src} width={shot.w} height={shot.h} alt={shot.alt} loading={i === 0 ? 'eager' : 'lazy'} decoding="async" />
                  <span className="wcard__arrow" aria-hidden="true">
                    ↗
                  </span>
                  <span className="wcard__bar">
                    <span id={`wc-${p.id}`} className="wcard__title">
                      {p.title}
                    </span>
                    <span className="wcard__tags">
                      {p.tags.map((t) => (
                        <span key={t}>{t}</span>
                      ))}
                    </span>
                  </span>
                  <span className="sr-only">Open case study</span>
                </button>
              </article>
            );
          })}

          <p className="work2__meta" aria-live="polite">
            <span>
              {String(active + 1).padStart(2, '0')} / {String(projects.length).padStart(2, '0')}
            </span>
            <span>{projects[active].category}</span>
            {projects[active].live ? (
              <a href={projects[active].live} target="_blank" rel="noopener noreferrer">
                Visit live site ↗
              </a>
            ) : (
              <button type="button" onClick={() => onOpen(projects[active].id)}>
                View case study ↗
              </button>
            )}
          </p>
        </div>
      </div>
    </section>
  );
}
