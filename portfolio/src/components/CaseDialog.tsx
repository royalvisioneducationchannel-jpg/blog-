import { useEffect, useRef } from 'react';
import { projects } from '../content';
import { useInquiry } from '../motion';
import { BrowserFrame, Card, PhoneFrame } from './Frames';

const typeFor: Record<string, string> = {
  'Business Websites & Landing Pages': 'Business website',
  'WooCommerce Stores': 'WooCommerce store',
  'Meta Ads Campaigns': 'Meta ads campaign',
};

export default function CaseDialog({ id, onClose }: { id: string | null; onClose: () => void }) {
  const ref = useRef<HTMLDialogElement>(null);
  const { choose } = useInquiry();
  const p = projects.find((x) => x.id === id);

  useEffect(() => {
    const d = ref.current;
    if (!d) return;
    if (p && !d.open) d.showModal();
    if (!p && d.open) d.close();
  }, [p]);

  return (
    <dialog ref={ref} className="case" aria-labelledby="case-title" onClose={onClose} onClick={(e) => e.target === ref.current && onClose()}>
      {p && (
        <div className="case__body">
          <button type="button" className="case__close" onClick={onClose} aria-label="Close case study">
            ✕
          </button>
          <p className="kicker">{p.category}</p>
          <h2 id="case-title" className="h2">
            {p.title}
          </h2>

          <div className="case__hero">
            {p.desktop && <BrowserFrame shot={p.desktop} url={p.live} />}
            {p.creative && <Card shot={p.creative[0]} />}
          </div>

          <div className="case__cols">
            <section>
              <h3 className="h4">Brief &amp; context</h3>
              <p>{p.brief}</p>
              <h3 className="h4">My responsibilities</h3>
              <p>{p.role}</p>
              <ul className="ticks">
                {p.delivered.map((d) => (
                  <li key={d}>{d}</li>
                ))}
              </ul>
            </section>
            <section>
              <h3 className="h4">Design &amp; implementation decisions</h3>
              <ul className="ticks">
                {p.decisions.map((d) => (
                  <li key={d}>{d}</li>
                ))}
              </ul>
              <h3 className="h4">{p.outcomeVerified ? 'Verified result' : 'What was delivered'}</h3>
              <p>{p.outcome}</p>
              <p className="tags">
                {p.tools.map((t) => (
                  <span key={t}>{t}</span>
                ))}
              </p>
            </section>
          </div>

          {(p.mobile || (p.creative && p.creative[1])) && (
            <div className="case__more">
              {p.mobile && <PhoneFrame shot={p.mobile} />}
              {p.creative?.[1] && <Card shot={p.creative[1]} />}
            </div>
          )}

          <div className="case__ctas">
            <button
              type="button"
              className="btn btn--primary"
              onClick={() => {
                onClose();
                choose({ projectType: typeFor[p.service] ?? 'Other' });
              }}
            >
              Discuss a similar project
            </button>
            {p.live && (
              <a className="btn btn--ghost-dark" href={p.live} target="_blank" rel="noopener noreferrer">
                Visit Live Website ↗
              </a>
            )}
          </div>
        </div>
      )}
    </dialog>
  );
}
