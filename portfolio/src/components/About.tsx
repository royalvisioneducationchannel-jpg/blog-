import { profile } from '../content';

export default function About() {
  return (
    <section className="about section--warm" id="about" aria-labelledby="about-title">
      <div className="container about__grid">
        <figure className="about__photo">
          <img src={profile.portraitFramed} width={720} height={860} alt={`${profile.name} in a lavender shirt, arms crossed`} loading="lazy" decoding="async" />
          <figcaption className="annotation" aria-hidden="true">
            <span>The person behind the work.</span>
            <svg viewBox="0 0 120 60" width="120" height="60">
              <path d="M110 6 C 80 10, 40 18, 14 48" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" />
              <path d="M10 34 L 13 50 L 28 44" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" />
            </svg>
          </figcaption>
        </figure>

        <div className="about__copy">
          <p className="kicker">About me</p>
          <h2 id="about-title" className="h2">
            Hi, I’m Saleem.
          </h2>
          <p className="about__lead">
            I care about the details that make a website useful: clear communication, thoughtful design, and a smooth experience for
            the people using it.
          </p>
          <p>
            I’m a WordPress &amp; Elementor developer and Meta Ads specialist based in {profile.location}. I build websites, WooCommerce
            stores and lead-generation pages, and I set up Facebook and Instagram campaigns that are tracked and reported honestly.
          </p>
          <h3 className="h4">How I work with clients</h3>
          <ul className="ticks">
            <li>A clear plan and price before we start</li>
            <li>A full backup before any change on a live site</li>
            <li>Tested on mobile, tablet and desktop</li>
            <li>Partner or temporary access only — I never need your passwords</li>
            <li>Support after delivery</li>
          </ul>
          <p className="about__langs">Languages: {profile.languages.join(' · ')}</p>
          <p className="signature" aria-label={profile.name}>
            <span>Muhammad Saleem</span>
            <svg viewBox="0 0 240 18" width="240" height="18" aria-hidden="true">
              <path d="M2 12 C 50 2, 110 16, 160 8 S 220 6, 238 10" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" />
            </svg>
          </p>
        </div>
      </div>
    </section>
  );
}
