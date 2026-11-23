import { useEffect, useState } from 'react';
import { profile } from '../content';
import { useInquiry, useMotion } from '../motion';

const links: [string, string][] = [
  ['#work', 'Work'],
  ['#services', 'Services'],
  ['#about', 'About'],
  ['#pricing', 'Pricing'],
];

export default function Header() {
  const { motion, toggle } = useMotion();
  const { choose } = useInquiry();
  const [scrolled, setScrolled] = useState(false);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    const on = () => setScrolled(window.scrollY > 30);
    on();
    window.addEventListener('scroll', on, { passive: true });
    return () => window.removeEventListener('scroll', on);
  }, []);

  return (
    <header className={`hd ${scrolled ? 'is-scrolled' : ''}`}>
      <a className="skip" href="#main">
        Skip to content
      </a>
      <div className="hd__inner">
        <a className="hd__brand" href="#top" aria-label={`${profile.name}, back to top`}>
          <span className="hd__mark" aria-hidden="true">
            MS
          </span>
          <span className="hd__name">{profile.name}</span>
        </a>
        <nav id="hd-nav" className={`hd__nav ${open ? 'is-open' : ''}`} aria-label="Main">
          {links.map(([h, l]) => (
            <a key={h} href={h} onClick={() => setOpen(false)}>
              {l}
            </a>
          ))}
        </nav>
        <div className="hd__end">
          <span className="hd__status">
            <i aria-hidden="true" /> Available for projects
          </span>
          <button type="button" className="hd__motion" aria-pressed={!motion} onClick={toggle} title={motion ? 'Pause animations' : 'Play animations'}>
            <span aria-hidden="true">{motion ? '❚❚' : '▶'}</span>
            <span className="sr-only">{motion ? 'Pause animations' : 'Play animations'}</span>
          </button>
          <button type="button" className="btn btn--accent btn--sm" data-magnetic onClick={() => choose()}>
            Let’s talk
          </button>
          <button type="button" className="hd__menu" aria-expanded={open} aria-controls="hd-nav" onClick={() => setOpen((o) => !o)}>
            <span aria-hidden="true" className={open ? 'x' : ''}>
              <i />
              <i />
            </span>
            <span className="sr-only">{open ? 'Close menu' : 'Open menu'}</span>
          </button>
        </div>
      </div>
    </header>
  );
}
