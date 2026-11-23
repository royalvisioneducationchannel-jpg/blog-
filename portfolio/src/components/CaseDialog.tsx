import { useEffect, useRef } from 'react';
import { projects, type Shot } from '../content';
import { useInquiry } from '../motion';

const typeFor: Record<string, string> = {
  'Business Websites & Landing Pages': 'Business website',
  'WooCommerce Stores': 'WooCommerce store',
  'Meta Ads Campaigns': 'Meta ads campaign',
};

const Img = ({ s, className }: { s: Shot; className?: string }) => (
  <img className={className} src={s.src} width={s.w} height={s.h} alt={s.alt} loading="lazy" decoding="async" />
);

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
    <dialog ref={ref} className="case2" aria-labelledby="case-title" onClose={onClose} onClick={(e) => e.target === ref.current && onClose()}>
      {p && (
        <div className="case2__body">
          <button type="button" className="x" onClick={onClose} aria-label="Close case study">
            ✕
          </button>
          <p className="caps">{p.category}</p>
          <h2 id="case-title">{p.title}</h2>
          <div className="case2__shots">
            {p.desktop && <Img s={p.desktop} className="shot shot--desk" />}
            {p.mobile && <Img s={p.mobile} className="shot shot--phone" />}
            {p.creative?.map((c) => <Img key={c.src} s={c} className="shot shot--desk" />)}
          </div>
          <div className="case2__cols">
            <div>
              <h3>Brief</h3>
              <p>{p.brief}</p>
              <h3>My role</h3>
              <p>{p.role}</p>
              <h3>Delivered</h3>
              <ul>
                {p.delivered.map((d) => (
                  <li key={d}>{d}</li>
                ))}
              </ul>
            </div>
            <div>
              <h3>Decisions</h3>
              <ul>
                {p.decisions.map((d) => (
                  <li key={d}>{d}</li>
                ))}
              </ul>
              <h3>{p.outcomeVerified ? 'Verified result' : 'Outcome'}</h3>
              <p>{p.outcome}</p>
              <h3>Tools</h3>
              <p className="case2__tools">{p.tools.join(' · ')}</p>
            </div>
          </div>
          <div className="case2__ctas">
            <button
              type="button"
              className="pillbtn pillbtn--accent"
              onClick={() => {
                onClose();
                choose({ projectType: typeFor[p.service] ?? 'Other' });
              }}
            >
              Discuss a similar project
            </button>
            {p.live && (
              <a className="pillbtn" href={p.live} target="_blank" rel="noopener noreferrer">
                Visit live site ↗
              </a>
            )}
          </div>
        </div>
      )}
    </dialog>
  );
}
