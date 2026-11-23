import { useState } from 'react';
import { metaAdsTiers, packages, profile, quickServices } from '../content';
import { useInquiry } from '../motion';

const typeFor: Record<string, string> = {
  'landing-page': 'Landing page',
  'business-website': 'Business website',
  'website-campaign': 'Website + campaign',
};
const tabs = ['Websites', 'Meta Ads', 'Quick fixes'] as const;

export default function Pricing() {
  const { choose } = useInquiry();
  const [tab, setTab] = useState<(typeof tabs)[number]>('Websites');

  return (
    <section className="price3" id="pricing" aria-labelledby="price-title">
      <div className="wrap sec-head sec-head--center">
        <p className="eyebrow">Pricing</p>
        <h2 id="price-title" className="h2">
          Clear scope. <em>No surprises.</em>
        </h2>
        <div className="tabs" role="tablist" aria-label="Pricing category">
          {tabs.map((t) => (
            <button key={t} type="button" role="tab" id={`tab-${t}`} aria-selected={tab === t} aria-controls="price-panel" className={tab === t ? 'is-on' : ''} onClick={() => setTab(t)}>
              {t}
            </button>
          ))}
        </div>
      </div>

      <div className="wrap" id="price-panel" role="tabpanel" aria-labelledby={`tab-${tab}`}>
        {tab === 'Websites' && (
          <div className="plans">
            {packages.map((p, i) => (
              <article key={p.id} className={`plan ${i === 1 ? 'plan--hi' : ''}`}>
                <h3>{p.name}</h3>
                <p className="plan__for">{p.forWho}</p>
                <p className="plan__price">{p.price ?? 'Custom quote'}</p>
                {p.note && <p className="plan__note">{p.note}</p>}
                <ul className="checks">
                  {p.deliverables.map((d) => (
                    <li key={d}>{d}</li>
                  ))}
                </ul>
                <button type="button" className={`btn ${i === 1 ? 'btn--accent' : 'btn--light'} btn--block`} onClick={() => choose({ projectType: typeFor[p.id], pkg: p.name })}>
                  {p.price ? `Choose ${p.name}` : 'Request a quote'}
                </button>
              </article>
            ))}
          </div>
        )}
        {tab === 'Meta Ads' && (
          <>
            <div className="plans">
              {metaAdsTiers.map((t, i) => (
                <article key={t.name} className={`plan ${i === 1 ? 'plan--hi' : ''}`}>
                  <h3>{t.name}</h3>
                  <p className="plan__price">{t.price}</p>
                  <ul className="checks">
                    {t.items.map((it) => (
                      <li key={it}>{it}</li>
                    ))}
                  </ul>
                  <button type="button" className={`btn ${i === 1 ? 'btn--accent' : 'btn--light'} btn--block`} onClick={() => choose({ projectType: 'Meta ads campaign', pkg: `Meta Ads — ${t.name} (${t.price})` })}>
                    Choose {t.name}
                  </button>
                </article>
              ))}
            </div>
            <p className="price3__note">Prices from my Upwork Project Catalog. Ad spend is paid by you directly to Meta and is not included.</p>
          </>
        )}
        {tab === 'Quick fixes' && (
          <ul className="fixes">
            {quickServices.map((q) => (
              <li key={q.name}>
                <span>{q.name}</span>
                <strong>{q.price}</strong>
                <button type="button" className="tlink" onClick={() => choose({ projectType: 'Fix or improvement', pkg: q.name })}>
                  Request →
                </button>
              </li>
            ))}
          </ul>
        )}
        <p className="price3__more">
          <a href={profile.links.upwork} target="_blank" rel="noopener noreferrer">
            See all packages on Upwork ↗
          </a>
          <button type="button" className="tlink" onClick={() => choose({ projectType: 'Other', pkg: 'Custom package' })}>
            Need a different scope? Build a custom package →
          </button>
        </p>
      </div>
    </section>
  );
}
