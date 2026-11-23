import { services, skillGroups, projects } from '../content';
import { useInquiry } from '../motion';

const typeFor: Record<string, string> = {
  'Business Websites & Landing Pages': 'Business website',
  'WooCommerce Stores': 'WooCommerce store',
  'Meta Ads Campaigns': 'Meta ads campaign',
  'Fixes, Tracking & Creatives': 'Fix or improvement',
};

export default function Services({ onOpen }: { onOpen: (id: string) => void }) {
  const { choose } = useInquiry();
  return (
    <section className="services section--light" id="services" aria-labelledby="services-title">
      <div className="container">
        <header className="section-head section-head--split">
          <div>
            <p className="kicker">Services</p>
            <h2 id="services-title" className="h2">
              What I can build <br className="br-lg" />
              for your business.
            </h2>
          </div>
          <p className="section-lead">Organized around what you need, not around tools. Every service comes with a clear scope and price before we start.</p>
        </header>

        <div className="svc-grid">
          {services.map((s, i) => {
            const ex = projects.find((p) => p.id === s.example);
            return (
              <article key={s.title} className={`svc svc--${i % 2 ? 'b' : 'a'}`}>
                <div className="svc__thumb float-soft" aria-hidden="true">
                  <img src={s.thumb} alt="" width={320} height={225} loading="lazy" decoding="async" />
                </div>
                <p className="svc__num">{String(i + 1).padStart(2, '0')}</p>
                <h3 className="h3">{s.title}</h3>
                <p className="svc__who">
                  <strong>For:</strong> {s.who}
                </p>
                <ul className="ticks">
                  {s.deliver.map((d) => (
                    <li key={d}>{d}</li>
                  ))}
                </ul>
                <p className="svc__receive">
                  <strong>You receive:</strong> {s.receive}
                </p>
                <div className="svc__ctas">
                  <button type="button" className="link-btn" onClick={() => choose({ projectType: typeFor[s.title] })}>
                    Discuss this service →
                  </button>
                  {ex && (
                    <button type="button" className="link-btn link-btn--muted" onClick={() => onOpen(ex.id)}>
                      Example: {ex.title}
                    </button>
                  )}
                </div>
              </article>
            );
          })}
        </div>

        <div className="skills" aria-labelledby="skills-title">
          <h3 id="skills-title" className="h4 skills__title">
            Skills behind the work
          </h3>
          <div className="skills__grid">
            {skillGroups.map((g) => (
              <div key={g.title} className="skills__group">
                <h4>{g.title}</h4>
                <ul>
                  {g.items.map((it) => (
                    <li key={it}>{it}</li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
