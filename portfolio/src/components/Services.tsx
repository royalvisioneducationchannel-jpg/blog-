import { services, projects, skillGroups } from '../content';
import { useInquiry } from '../motion';

const icons = [
  <svg key="a" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5"><rect x="3" y="4" width="18" height="16" rx="2" /><path d="M3 9h18M7 6.5h.01M10 6.5h.01" /></svg>,
  <svg key="b" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M5 8h14l-1 12H6L5 8Z" /><path d="M9 8V6a3 3 0 0 1 6 0v2" /></svg>,
  <svg key="c" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M4 10v4h3l7 4V6L7 10H4Z" /><path d="M17 9a4 4 0 0 1 0 6" /></svg>,
  <svg key="d" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M14.5 5.5a4 4 0 0 0 5 5L12 18a2.1 2.1 0 0 1-3-3l7.5-7.5Z" /><path d="M7 17l-3 3" /></svg>,
];

const typeFor: Record<string, string> = {
  'Business Websites & Landing Pages': 'Business website',
  'WooCommerce Stores': 'WooCommerce store',
  'Meta Ads Campaigns': 'Meta ads campaign',
  'Fixes, Tracking & Creatives': 'Fix or improvement',
};

export default function Services({ onOpen }: { onOpen: (id: string) => void }) {
  const { choose } = useInquiry();
  const spot = (e: React.PointerEvent<HTMLElement>) => {
    const r = e.currentTarget.getBoundingClientRect();
    e.currentTarget.style.setProperty('--mx', `${e.clientX - r.left}px`);
    e.currentTarget.style.setProperty('--my', `${e.clientY - r.top}px`);
  };
  return (
    <section className="svc3" id="services" aria-labelledby="svc-title">
      <div className="wrap sec-head">
        <p className="eyebrow">Services</p>
        <h2 id="svc-title" className="h2">
          Everything your business needs to <em>grow online.</em>
        </h2>
      </div>
      <div className="wrap svc3__grid">
        {services.map((s, i) => {
          const ex = projects.find((p) => p.id === s.example);
          return (
            <article key={s.title} className="scard" onPointerMove={spot}>
              <div className="scard__top">
                <span className="scard__icon" aria-hidden="true">
                  {icons[i]}
                </span>
                <span className="scard__n">{String(i + 1).padStart(2, '0')}</span>
              </div>
              <h3>{s.title}</h3>
              <p className="scard__who">{s.who}</p>
              <ul className="checks">
                {s.deliver.map((d) => (
                  <li key={d}>{d}</li>
                ))}
              </ul>
              <p className="scard__get">{s.receive}</p>
              <div className="scard__links">
                <button type="button" className="tlink" onClick={() => choose({ projectType: typeFor[s.title] })}>
                  Discuss this service →
                </button>
                {ex && (
                  <button type="button" className="tlink tlink--muted" onClick={() => onOpen(ex.id)}>
                    See example
                  </button>
                )}
              </div>
            </article>
          );
        })}
      </div>
      <div className="wrap skills3">
        {skillGroups.map((g) => (
          <div key={g.title}>
            <h3>{g.title}</h3>
            <p>{g.items.join(' · ')}</p>
          </div>
        ))}
      </div>
    </section>
  );
}
