import { useEffect, useRef, useState } from 'react';
import { process } from '../content';

export default function Process() {
  const [active, setActive] = useState(-1);
  const items = useRef<(HTMLLIElement | null)[]>([]);

  useEffect(() => {
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) setActive((a) => Math.max(a, Number((e.target as HTMLElement).dataset.i)));
        });
      },
      { rootMargin: '0px 0px -35% 0px' },
    );
    items.current.forEach((el) => el && io.observe(el));
    return () => io.disconnect();
  }, []);

  return (
    <section className="process section--dark" id="process" aria-labelledby="process-title">
      <div className="container">
        <header className="section-head">
          <p className="kicker">How we work</p>
          <h2 id="process-title" className="h2">
            Four simple steps, <span className="muted">from idea to launch.</span>
          </h2>
        </header>
        <ol className="steps4">
          {process.map((s, i) => (
            <li key={s.n} data-i={i} ref={(el) => (items.current[i] = el)} className={i <= active ? 'is-on' : ''}>
              <span className="steps4__n">{s.n}</span>
              <h3 className="h4">{s.title}</h3>
              <p>{s.text}</p>
            </li>
          ))}
        </ol>
      </div>
    </section>
  );
}
