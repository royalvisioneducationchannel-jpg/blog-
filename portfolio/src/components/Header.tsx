import { useMotion } from '../motion';

const links: [string, string][] = [
  ['#work', 'Work'],
  ['#services', 'Services'],
  ['#about', 'About'],
  ['#packages', 'Packages'],
  ['#connect', 'Contact'],
];

export default function Header() {
  const { motion, toggle } = useMotion();
  return (
    <header className="hdr">
      <a className="skip" href="#main">
        Skip to content
      </a>
      <nav className="hdr__nav" aria-label="Main">
        <a className="hdr__logo" href="#top" aria-label="Muhammad Saleem, back to top">
          ms<span>.</span>
        </a>
        {links.map(([href, label]) => (
          <a key={href} href={href}>
            {label}
          </a>
        ))}
        <button type="button" className="hdr__motion" aria-pressed={!motion} onClick={toggle}>
          {motion ? 'Motion on' : 'Motion off'}
        </button>
      </nav>
    </header>
  );
}
