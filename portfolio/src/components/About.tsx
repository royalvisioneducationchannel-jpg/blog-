import { process, profile } from '../content';

export default function About() {
  return (
    <section className="about3" id="about" aria-labelledby="about-title">
      <div className="wrap about3__grid">
        <div className="about3__media">
          <div className="about3__photo">
            <img src={profile.portrait} width={580} height={769} alt={`Portrait of ${profile.name}`} loading="lazy" decoding="async" />
          </div>
          <svg className="badge" viewBox="0 0 120 120" aria-hidden="true">
            <defs>
              <path id="circ" d="M60,60 m-46,0 a46,46 0 1,1 92,0 a46,46 0 1,1 -92,0" />
            </defs>
            <text>
              <textPath href="#circ">WORDPRESS • ELEMENTOR • META ADS • WOOCOMMERCE •</textPath>
            </text>
          </svg>
        </div>
        <div className="about3__copy">
          <p className="eyebrow">About me</p>
          <h2 id="about-title" className="h2">
            The person <em>behind the work.</em>
          </h2>
          <p className="about3__lead">
            I’m {profile.name}, a WordPress &amp; Elementor developer and Meta Ads specialist based in {profile.location}.
          </p>
          <p>
            I build business websites, WooCommerce stores and lead-generation pages, and I set up Facebook and Instagram campaigns. I care
            about the details that make a website useful: clear communication, thoughtful design and a smooth experience for the people using
            it.
          </p>
          <ul className="checks checks--two">
            <li>A clear plan and price before we start</li>
            <li>A full backup before any change</li>
            <li>Tested on mobile, tablet and desktop</li>
            <li>Partner access only, never your passwords</li>
          </ul>
          <p className="about3__langs">Languages: {profile.languages.join(' · ')}</p>
        </div>
      </div>

      <div className="wrap proc" aria-labelledby="proc-title">
        <h3 id="proc-title" className="proc__title">
          How we work together
        </h3>
        <ol className="proc__list">
          {process.map((s) => (
            <li key={s.n}>
              <span className="proc__n">{s.n}</span>
              <h4>{s.title}</h4>
              <p>{s.text}</p>
            </li>
          ))}
        </ol>
      </div>
    </section>
  );
}
