import { useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { hero, statement } from '../content';
import { useMotion } from '../motion';

gsap.registerPlugin(ScrollTrigger);

function Chars({ text, className }: { text: string; className?: string }) {
  return (
    <span className={className} aria-hidden="true">
      {text.split(' ').map((w, wi) => (
        <span className="word" key={wi}>
          {[...w].map((c, ci) => (
            <span className="char" key={ci}>
              {c}
            </span>
          ))}
        </span>
      ))}
    </span>
  );
}

export default function Hero() {
  const { motion } = useMotion();
  const root = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      if (!motion) {
        gsap.set('.char, .hero2__meta > *', { clearProps: 'all' });
        return;
      }
      gsap.from('.hero2__title .char', { yPercent: 110, opacity: 0, duration: 0.9, ease: 'power4.out', stagger: 0.018 });
      gsap.from('.hero2__meta > *', { opacity: 0, y: 16, duration: 0.8, ease: 'power3.out', stagger: 0.1, delay: 0.6 });

      // The glow rises from the bottom of the hero into the dark statement section.
      gsap.fromTo(
        '.glow',
        { yPercent: 55, scaleX: 0.9 },
        { yPercent: -10, scaleX: 1.25, ease: 'none', scrollTrigger: { trigger: '.hero2', start: 'top top', end: 'bottom top', scrub: true } },
      );
      gsap.to('.hero2__title', { yPercent: -30, opacity: 0, ease: 'none', scrollTrigger: { trigger: '.hero2', start: 'top top', end: 'bottom top', scrub: true } });

      // Statement words light up one by one while scrolling.
      gsap.fromTo(
        '.stmt__text .w',
        { opacity: 0.18 },
        { opacity: 1, stagger: 0.05, ease: 'none', scrollTrigger: { trigger: '.stmt', start: 'top 60%', end: 'bottom 70%', scrub: true } },
      );
    }, root);
    return () => ctx.revert();
  }, [motion]);

  return (
    <div ref={root}>
      <section className="hero2" id="top" aria-labelledby="hero-title">
        <h1 id="hero-title" className="hero2__title" aria-label={`${hero.line1} ${hero.line2}`}>
          <Chars text={hero.line1} className="l1" />
          <Chars text={hero.line2} className="l2" />
        </h1>
        <div className="hero2__meta">
          <ul className="caps">
            {hero.left.map((t) => (
              <li key={t}>{t}</li>
            ))}
          </ul>
          <a className="pill" href="#work">
            Scroll to explore
          </a>
          <ul className="caps caps--right">
            {hero.right.map((t) => (
              <li key={t}>{t}</li>
            ))}
          </ul>
        </div>
        <div className="glow" aria-hidden="true">
          <span />
        </div>
      </section>

      <section className="stmt dark" aria-labelledby="stmt-title">
        <div className="stmt__inner">
          <h2 id="stmt-title" className="stmt__title">
            {statement.title}
          </h2>
          <p className="stmt__text">
            {statement.text.split(' ').map((w, i) => (
              <span className="w" key={i}>
                {w}{' '}
              </span>
            ))}
          </p>
        </div>
      </section>
    </div>
  );
}
