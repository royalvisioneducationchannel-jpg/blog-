import { metaAdsTiers, packages, profile, quickServices } from '../content';
import { useInquiry } from '../motion';

const typeFor: Record<string, string> = {
  'landing-page': 'Landing page',
  'business-website': 'Business website',
  'website-campaign': 'Website + campaign',
};

export default function Packages() {
  const { choose } = useInquiry();
  return (
    <section className="packages section--light" id="packages" aria-labelledby="packages-title">
      <div className="container">
        <header className="section-head section-head--split">
          <div>
            <p className="kicker">Packages</p>
            <h2 id="packages-title" className="h2">
              Clear scope. <span className="muted">No surprises.</span>
            </h2>
          </div>
          <p className="section-lead">Choose a starting point. Every package is confirmed in writing before work begins, and you can adjust the scope.</p>
        </header>

        <div className="pkg-grid">
          {packages.map((p, i) => (
            <article key={p.id} className={`pkg pkg--${i + 1}`}>
              <h3 className="h3">{p.name}</h3>
              <p className="pkg__for">{p.forWho}</p>
              <p className="pkg__price">{p.price ?? 'Custom quote'}</p>
              <ul className="ticks">
                {p.deliverables.map((d) => (
                  <li key={d}>{d}</li>
                ))}
              </ul>
              <dl className="pkg__meta">
                {p.limits && (
                  <div>
                    <dt>Scope</dt>
                    <dd>{p.limits}</dd>
                  </div>
                )}
                {p.revisions && (
                  <div>
                    <dt>Revisions</dt>
                    <dd>{p.revisions}</dd>
                  </div>
                )}
                {p.delivery && (
                  <div>
                    <dt>Delivery</dt>
                    <dd>{p.delivery}</dd>
                  </div>
                )}
              </dl>
              {p.note && <p className="pkg__note">{p.note}</p>}
              <button type="button" className="btn btn--dark" onClick={() => choose({ projectType: typeFor[p.id], pkg: p.name })}>
                {p.price ? `Choose ${p.name}` : 'Request a Quote'}
              </button>
            </article>
          ))}
        </div>

        <div className="pkg-extra">
          <div className="pkg-ads">
            <div className="pkg-ads__head">
              <h3 className="h4">Meta Ads campaign setup</h3>
              <p>Live prices from my Upwork Project Catalog. Ad spend is paid by you directly to Meta.</p>
            </div>
            <div className="pkg-ads__tiers">
              {metaAdsTiers.map((t) => (
                <div key={t.name} className="tier">
                  <p className="tier__name">{t.name}</p>
                  <p className="tier__price">{t.price}</p>
                  <ul>
                    {t.items.map((it) => (
                      <li key={it}>{it}</li>
                    ))}
                  </ul>
                  <button type="button" className="link-btn" onClick={() => choose({ projectType: 'Meta ads campaign', pkg: `Meta Ads — ${t.name} (${t.price})` })}>
                    Select {t.name} →
                  </button>
                </div>
              ))}
            </div>
          </div>

          <div className="pkg-quick">
            <h3 className="h4">Quick fixes</h3>
            <ul>
              {quickServices.map((q) => (
                <li key={q.name}>
                  <span>{q.name}</span>
                  <strong>{q.price}</strong>
                </li>
              ))}
            </ul>
            <a className="link-btn" href={profile.links.upwork} target="_blank" rel="noopener noreferrer">
              See all packages on Upwork ↗
            </a>
          </div>
        </div>

        <div className="pkg-custom">
          <p>Need a different scope?</p>
          <button type="button" className="btn btn--primary" onClick={() => choose({ projectType: 'Other', pkg: 'Custom package' })}>
            Build a custom package
          </button>
        </div>
      </div>
    </section>
  );
}
