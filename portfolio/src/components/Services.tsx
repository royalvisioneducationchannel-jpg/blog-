import { services, projects } from '../content';
import { useInquiry } from '../motion';

const icons = [
  // browser
  <svg key="a" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5"><rect x="3" y="4" width="18" height="16" rx="2" /><path d="M3 9h18M7 6.5h.01M10 6.5h.01" /></svg>,
  // bag
  <svg key="b" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M5 8h14l-1 12H6L5 8Z" /><path d="M9 8V6a3 3 0 0 1 6 0v2" /></svg>,
  // megaphone
  <svg key="c" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M4 10v4h3l7 4V6L7 10H4Z" /><path d="M17 9a4 4 0 0 1 0 6" /></svg>,
  // wrench
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
  return (
    <section className="svc2 dark" id="services" aria-labelledby="services-title">
      <h2 id="services-title" className="display center">
        Services
      </h2>
      <div className="svc2__list">
        {services.map((s, i) => {
          const ex = projects.find((p) => p.id === s.example);
          return (
            <article key={s.title} className="srow" style={{ ['--i' as string]: i }}>
              <header className="srow__head">
                <h3>{s.title}</h3>
                <span className="srow__icon" aria-hidden="true">
                  {icons[i % icons.length]}
                </span>
              </header>
              <div className="srow__body">
                <p className="srow__desc">
                  {s.who} {s.receive}
                </p>
                <h4>What&apos;s included?</h4>
                <ul>
                  {s.deliver.map((d) => (
                    <li key={d}>{d}</li>
                  ))}
                </ul>
                <p className="srow__links">
                  <button type="button" onClick={() => choose({ projectType: typeFor[s.title] })}>
                    Discuss this service →
                  </button>
                  {ex && (
                    <button type="button" onClick={() => onOpen(ex.id)}>
                      Example: {ex.title}
                    </button>
                  )}
                </p>
              </div>
            </article>
          );
        })}
      </div>
      <div className="svc2__cta">
        <button type="button" className="orb" onClick={() => choose()}>
          Contact
        </button>
      </div>
    </section>
  );
}
