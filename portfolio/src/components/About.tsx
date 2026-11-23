import { profile } from '../content';

export default function About() {
  return (
    <section className="about2 light" id="about" aria-labelledby="about-title">
      <h2 id="about-title" className="display center">
        About
      </h2>
      <div className="wrap">
        <h3 className="rowtitle">Something about me</h3>
        <div className="about2__grid">
          <img
            className="about2__photo"
            src={profile.portrait}
            width={580}
            height={769}
            alt={`Portrait of ${profile.name}`}
            loading="lazy"
            decoding="async"
          />
          <div className="about2__text">
            <p>
              I’m {profile.name}, a WordPress &amp; Elementor developer and Meta Ads specialist based in {profile.location}. I build
              business websites, WooCommerce stores and lead-generation pages, and I set up Facebook and Instagram campaigns.
            </p>
            <p>I care about the details that make a website useful: clear communication, thoughtful design, and a smooth experience for the people using it.</p>
            <p>
              My way of working is simple. You get a clear plan and price before we start, a full backup before any change on a live site,
              testing on mobile, tablet and desktop, and partner access only, so I never need your passwords.
            </p>
            <p className="about2__langs">Languages: {profile.languages.join(' · ')}</p>
          </div>
        </div>
      </div>
    </section>
  );
}
