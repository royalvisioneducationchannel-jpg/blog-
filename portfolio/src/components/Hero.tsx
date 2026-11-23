import { Component, lazy, Suspense, useCallback, useEffect, useRef, useState, type ReactNode } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { profile, projects } from '../content';
import { useMotion } from '../motion';
import { BrowserFrame, Card, PhoneFrame } from './Frames';
import type { SceneRefs } from './HeroScene';

gsap.registerPlugin(ScrollTrigger);

const HeroScene = lazy(() => import('./HeroScene'));

class SceneBoundary extends Component<{ children: ReactNode; onError: () => void }, { failed: boolean }> {
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

function hasWebGL() {
  try {
    const c = document.createElement('canvas');
    return !!(c.getContext('webgl2') || c.getContext('webgl'));
  } catch {
    return false;
  }
}

const store = projects.find((p) => p.id === 'royal-vision-store')!;
const law = projects.find((p) => p.id === 'royal-vision-law')!;
const edu = projects.find((p) => p.id === 'royal-vision-education')!;
const ads = projects.find((p) => p.id === 'meta-ads-study-abroad')!;

export default function Hero() {
  const { motion } = useMotion();
  const root = useRef<HTMLElement>(null);
  const stage = useRef<HTMLDivElement>(null);
  const refs = useRef<SceneRefs>({
    intro: { current: { v: 0 } },
    away: { current: { v: 0 } },
    pointer: { current: { x: 0, y: 0 } },
    invalidate: { current: null },
  }).current;
  const [want3D, setWant3D] = useState(false);
  const [ready, setReady] = useState(false);
  const [failed, setFailed] = useState(false);
  const [visible, setVisible] = useState(true);
  const [pageVisible, setPageVisible] = useState(true);

  // Load the 3D scene after first paint, only when WebGL exists.
  useEffect(() => {
    if (!hasWebGL()) return;
    const ric = (window as unknown as { requestIdleCallback?: (cb: () => void) => number }).requestIdleCallback;
    const id = ric ? ric(() => setWant3D(true)) : window.setTimeout(() => setWant3D(true), 400);
    return () => {
      if (!ric) window.clearTimeout(id);
    };
  }, []);

  // Pause rendering when the hero is offscreen or the tab is hidden.
  useEffect(() => {
    const io = new IntersectionObserver(([e]) => setVisible(e.isIntersecting), { threshold: 0 });
    if (root.current) io.observe(root.current);
    const onVis = () => setPageVisible(!document.hidden);
    document.addEventListener('visibilitychange', onVis);
    return () => {
      io.disconnect();
      document.removeEventListener('visibilitychange', onVis);
    };
  }, []);

  // Intro reveal (GSAP owns: text opacity/translate, intro progress value).
  useEffect(() => {
    const ctx = gsap.context(() => {
      if (!motion) {
        gsap.set('.hero [data-reveal]', { opacity: 1, y: 0 });
        refs.intro.current.v = 1;
        refs.invalidate.current?.();
        return;
      }
      const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
      tl.from('.hero [data-reveal="1"]', { opacity: 0, y: 26, duration: 0.7, stagger: 0.12 })
        .from('.hero [data-reveal="2"]', { opacity: 0, y: 18, duration: 0.6, stagger: 0.08 }, 0.25)
        .from('.hero [data-reveal="portrait"]', { opacity: 0, y: 30, scale: 0.97, duration: 0.9 }, 0.15)
        .from('.hero [data-reveal="front"]', { opacity: 0, y: 24, duration: 0.7, stagger: 0.12 }, 0.6)
        .to(refs.intro.current, { v: 1, duration: 1.4, ease: 'power2.out' }, 0.35);
    }, root);
    return () => ctx.revert();
  }, [motion, refs]);

  // Scroll away: one value drives both the 3D panels and DOM previews.
  useEffect(() => {
    const st = ScrollTrigger.create({
      trigger: root.current,
      start: 'top top',
      end: 'bottom top',
      onUpdate: (self) => {
        refs.away.current.v = self.progress;
        stage.current?.style.setProperty('--away', self.progress.toFixed(3));
        refs.invalidate.current?.();
      },
    });
    return () => st.kill();
  }, [refs]);

  // Pointer tilt / parallax (fine pointers only).
  const onMove = useCallback(
    (e: React.PointerEvent) => {
      if (!motion || e.pointerType !== 'mouse' || !stage.current) return;
      const r = stage.current.getBoundingClientRect();
      const x = ((e.clientX - r.left) / r.width) * 2 - 1;
      const y = ((e.clientY - r.top) / r.height) * 2 - 1;
      refs.pointer.current.x = Math.max(-1, Math.min(1, x));
      refs.pointer.current.y = Math.max(-1, Math.min(1, y));
      stage.current.style.setProperty('--px', refs.pointer.current.x.toFixed(3));
      stage.current.style.setProperty('--py', refs.pointer.current.y.toFixed(3));
    },
    [motion, refs],
  );
  const onLeave = useCallback(() => {
    refs.pointer.current.x = 0;
    refs.pointer.current.y = 0;
    stage.current?.style.setProperty('--px', '0');
    stage.current?.style.setProperty('--py', '0');
  }, [refs]);

  const onReady = useCallback(() => setReady(true), []);
  const onError = useCallback(() => setFailed(true), []);
  const use3D = want3D && !failed;

  return (
    <section className="hero" id="top" ref={root} aria-labelledby="hero-title">
      <div className="hero__inner container">
        <div className="hero__copy">
          <p className="eyebrow" data-reveal="2">
            <span className="dot" aria-hidden="true" /> <strong>{profile.name}</strong>
            <span className="eyebrow__role">WordPress Developer · Meta Ads Specialist</span>
          </p>
          <h1 id="hero-title" className="hero__title">
            <span data-reveal="1">Websites that stand out.</span>
            <span data-reveal="1" className="hero__title-accent">
              Campaigns built with purpose.
            </span>
          </h1>
          <p className="hero__lead" data-reveal="2">
            I help businesses build a stronger online presence with WordPress &amp; Elementor websites, WooCommerce stores and Meta
            advertising on Facebook and Instagram.
          </p>
          <div className="hero__ctas" data-reveal="2">
            <a className="btn btn--primary" href="#work">
              Explore My Work
            </a>
            <a className="btn btn--ghost" href="#contact">
              Discuss Your Project
            </a>
          </div>
          <ul className="hero__facts" data-reveal="2">
            <li>Based in {profile.location}</li>
            <li>
              <a href={profile.links.upwork} target="_blank" rel="noopener noreferrer">
                Hire me on Upwork ↗
              </a>
            </li>
          </ul>
        </div>

        <div
          className={`hero__stage ${use3D && ready ? 'is-3d' : ''}`}
          ref={stage}
          onPointerMove={onMove}
          onPointerLeave={onLeave}
        >
          {/* Static composition: shown first and kept as the fallback when 3D is unavailable. */}
          <div className="hero__static" aria-hidden={use3D && ready ? 'true' : undefined}>
            <div className="float-wrap fw-store">
              <BrowserFrame shot={store.desktop!} eager className="float f1" />
            </div>
            <div className="float-wrap fw-law">
              <BrowserFrame shot={law.desktop!} className="float f2" />
            </div>
            <div className="float-wrap fw-ads">
              <Card shot={ads.creative![0]} className="float f3" />
            </div>
          </div>

          {use3D && (
            <SceneBoundary onError={onError}>
              <Suspense fallback={null}>
                <HeroScene refs={refs} motion={motion} active={motion && visible && pageVisible} onReady={onReady} />
              </Suspense>
            </SceneBoundary>
          )}

          <div className="hero__portrait">
            <div className="portrait-inner" data-reveal="portrait">
              <div className="portrait-glow" aria-hidden="true" />
                <img
                src={profile.portrait}
                width={580}
                height={769}
                alt={`Portrait of ${profile.name}`}
              decoding="async"
              />
            </div>
          </div>

          <div className="float-wrap fw-phone">
            <div data-reveal="front">
              <PhoneFrame shot={edu.mobile!} eager className="float f4" />
            </div>
          </div>
          <div className="float-wrap fw-chip">
            <div data-reveal="front">
              <p className="skill-chip float f5">
                <span>Elementor</span>
                <span>WooCommerce</span>
                <span>Meta Ads</span>
              </p>
            </div>
          </div>
        </div>
      </div>
      <a className="hero__scroll" href="#work" aria-label="Scroll to selected work">
        <span aria-hidden="true" />
      </a>
    </section>
  );
}
