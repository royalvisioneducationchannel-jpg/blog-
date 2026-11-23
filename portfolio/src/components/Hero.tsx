import { Component, lazy, Suspense, useCallback, useEffect, useRef, useState, type ReactNode } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { hero, profile } from '../content';
import { useInquiry, useMotion } from '../motion';
import type { BlobRefs } from './Blob3D';

gsap.registerPlugin(ScrollTrigger);
const Blob3D = lazy(() => import('./Blob3D'));

class Boundary extends Component<{ children: ReactNode; onError: () => void }, { failed: boolean }> {
  state = { failed: false };
  static getDerivedStateFromError() {
    return { failed: true };
  }
  componentDidCatch() {
    this.props.onError();
  }
  render() {
    return this.state.failed ? null : this.props.children;
  }
}

const hasWebGL = () => {
  try {
    const c = document.createElement('canvas');
    return !!(c.getContext('webgl2') || c.getContext('webgl'));
  } catch {
    return false;
  }
};

export default function Hero({ ready }: { ready: boolean }) {
  const { motion } = useMotion();
  const { choose } = useInquiry();
  const root = useRef<HTMLElement>(null);
  const refs = useRef<BlobRefs>({ pointer: { current: { x: 0, y: 0 } }, scroll: { current: { v: 0 } } }).current;
  const [want3D, setWant3D] = useState(false);
  const [shown, setShown] = useState(false);
  const [failed, setFailed] = useState(false);
  const [visible, setVisible] = useState(true);

  useEffect(() => {
    if (hasWebGL()) setWant3D(true);
    const io = new IntersectionObserver(([e]) => setVisible(e.isIntersecting));
    if (root.current) io.observe(root.current);
    return () => io.disconnect();
  }, []);

  useEffect(() => {
    if (!ready) return;
    const ctx = gsap.context(() => {
      if (!motion) return;
      gsap.from('.h-line > span', { yPercent: 110, duration: 1.1, ease: 'power4.out', stagger: 0.09 });
      gsap.from('.h-fade', { opacity: 0, y: 18, duration: 0.9, ease: 'power3.out', stagger: 0.08, delay: 0.35 });
      gsap.from('.h-visual', { opacity: 0, scale: 0.92, duration: 1.4, ease: 'power3.out', delay: 0.1 });
      ScrollTrigger.create({
        trigger: root.current,
        start: 'top top',
        end: 'bottom top',
        onUpdate: (self) => (refs.scroll.current.v = self.progress),
      });
      gsap.to('.hero3__copy', { yPercent: -18, opacity: 0.2, ease: 'none', scrollTrigger: { trigger: root.current, start: 'top top', end: 'bottom top', scrub: true } });
    }, root);
    return () => ctx.revert();
  }, [ready, motion, refs]);

  const onMove = (e: React.PointerEvent) => {
    if (e.pointerType !== 'mouse') return;
    refs.pointer.current.x = (e.clientX / window.innerWidth) * 2 - 1;
    refs.pointer.current.y = (e.clientY / window.innerHeight) * 2 - 1;
  };
  const onReady = useCallback(() => setShown(true), []);

  return (
    <section className="hero3" id="top" ref={root} onPointerMove={onMove} aria-labelledby="hero-title">
      <div className="hero3__bg" aria-hidden="true" />
      <div className="hero3__inner">
        <div className="hero3__copy">
          <p className="hello h-fade">
            <img src={profile.portrait} alt="" width={580} height={769} />
            <span>{hero.greeting}</span>
            <span className="hello__role">WordPress Developer · Meta Ads Specialist</span>
          </p>
          <h1 id="hero-title" className="hero3__title">
            <span className="h-line">
              <span>{hero.lineA[0]}</span>
            </span>
            <span className="h-line">
              <span>
                <em>{hero.lineA[1]}</em>
              </span>
            </span>
            <span className="h-line">
              <span>{hero.lineB[0]}</span>
            </span>
            <span className="h-line">
              <span>
                <em>{hero.lineB[1]}</em>
              </span>
            </span>
          </h1>
          <p className="hero3__lead h-fade">{hero.lead}</p>
          <div className="hero3__ctas h-fade">
            <a className="btn btn--accent" href="#work" data-magnetic>
              Explore my work <span aria-hidden="true">↓</span>
            </a>
            <button type="button" className="btn btn--ghost" onClick={() => choose()} data-magnetic>
              Discuss your project
            </button>
          </div>
          <ul className="hero3__chips h-fade" aria-label="Main skills">
            {hero.chips.map((c) => (
              <li key={c}>{c}</li>
            ))}
          </ul>
        </div>

        <div className="hero3__visual h-visual" aria-hidden="true">
          <div className={`blob-fallback ${want3D && shown && !failed ? 'is-hidden' : ''}`} />
          {want3D && !failed && (
            <Boundary onError={() => setFailed(true)}>
              <Suspense fallback={null}>
                <Blob3D refs={refs} motion={motion} active={motion && visible} onReady={onReady} />
              </Suspense>
            </Boundary>
          )}
          <div className="orbit-card oc-1">
            <strong>6,521</strong>
            <span>conversations from Meta ads</span>
          </div>
          <div className="orbit-card oc-2">
            <strong>3 live sites</strong>
            <span>Store · Education · Law firm</span>
          </div>
        </div>
      </div>
      <a className="hero3__scroll" href="#work" aria-label="Scroll to selected work">
        <span />
      </a>
    </section>
  );
}
