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
    <section className="pkg2 light" id="packages" aria-labelledby="packages-title">
      <h2 id="packages-title" className="display center">
        Packages
      </h2>
      <div className="wrap">
        <h3 className="rowtitle">Clear scope, confirmed before we start</h3>
        <div className="prows">
          {packages.map((p) => (
            <article key={p.id} className="prow">
              <h4>{p.name}</h4>
              <p className="prow__for">{p.forWho}</p>
              <ul>
                {p.deliverables.map((d) => (
                  <li key={d}>{d}</li>
                ))}
              </ul>
              <div className="prow__end">
                <p className="prow__price">{p.price ?? 'Custom quote'}</p>
                {p.note && <p className="prow__note">{p.note}</p>}
                <button type="button" className="pillbtn" onClick={() => choose({ projectType: typeFor[p.id], pkg: p.name })}>
                  {p.price ? `Choose ${p.name}` : 'Request a Quote'}
                </button>
              </div>
            </article>
          ))}
        </div>

        <h3 className="rowtitle">Meta Ads campaign setup</h3>
        <p className="muted">Prices from my Upwork Project Catalog. Ad spend is paid by you directly to Meta.</p>
        <div className="tiers2">
          {metaAdsTiers.map((t) => (
            <article key={t.name} className="tier2">
              <h4>
                {t.name} <span>{t.price}</span>
              </h4>
              <ul>
                {t.items.map((it) => (
                  <li key={it}>{it}</li>
                ))}
              </ul>
              <button type="button" className="textbtn" onClick={() => choose({ projectType: 'Meta ads campaign', pkg: `Meta Ads — ${t.name} (${t.price})` })}>
                Select {t.name} →
              </button>
            </article>
          ))}
        </div>

        <h3 className="rowtitle">Quick fixes</h3>
        <ul className="quick2">
          {quickServices.map((q) => (
            <li key={q.name}>
              <span>{q.name}</span>
              <strong>{q.price}</strong>
            </li>
          ))}
        </ul>
        <p className="pkg2__more">
          <a href={profile.links.upwork} target="_blank" rel="noopener noreferrer">
            See all packages on Upwork ↗
          </a>
          <button type="button" className="textbtn" onClick={() => choose({ projectType: 'Other', pkg: 'Custom package' })}>
            Need a different scope? Build a custom package →
          </button>
        </p>
      </div>
    </section>
  );
}
