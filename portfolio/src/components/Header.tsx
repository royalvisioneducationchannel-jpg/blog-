import { useEffect, useState } from 'react';
import { profile } from '../content';
import { useMotion } from '../motion';

const links = [
  ['#work', 'Work'],
  ['#services', 'Services'],
  ['#about', 'About'],
  ['#packages', 'Packages'],
  ['#process', 'Process'],
];

export default function Header() {
  const { motion, toggle } = useMotion();
  const [open, setOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const on = () => setScrolled(window.scrollY > 24);
    on();
    window.addEventListener('scroll', on, { passive: true });
    return () => window.removeEventListener('scroll', on);
  }, []);

  useEffect(() => {
    if (!open) return;
    const onKey = (e: KeyboardEvent) => e.key === 'Escape' && setOpen(false);
    document.addEventListener('keydown', onKey);
    return () => document.removeEventListener('keydown', onKey);
  }, [open]);

  return (
    <header className={`site-header ${scrolled ? 'is-scrolled' : ''}`}>
      <a className="skip" href="#main">
        Skip to content
      </a>
      <div className="container site-header__inner">
        <a className="brand" href="#top" aria-label={`${profile.name}, back to top`}>
          <span className="brand__mark" aria-hidden="true">
            MS
          </span>
          <span className="brand__name">{profile.name}</span>
        </a>
        <nav id="site-nav" className={`nav ${open ? 'is-open' : ''}`} aria-label="Main">
          {links.map(([href, label]) => (
            <a key={href} href={href} onClick={() => setOpen(false)}>
              {label}
            </a>
          ))}
          <a className="btn btn--primary btn--sm nav__cta" href="#contact" onClick={() => setOpen(false)}>
            Discuss a Project
          </a>
        </nav>
        <div className="site-header__tools">
          <button type="button" className="motion-toggle" aria-pressed={!motion} onClick={toggle} title={motion ? 'Pause animations' : 'Play animations'}>
            <span aria-hidden="true">{motion ? '❚❚' : '▶'}</span>
            <span className="sr-only">{motion ? 'Pause decorative animations' : 'Play decorative animations'}</span>
          </button>
          <button type="button" className="menu-btn" aria-expanded={open} aria-controls="site-nav" onClick={() => setOpen((o) => !o)}>
            <span aria-hidden="true" className={`burger ${open ? 'x' : ''}`}>
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
