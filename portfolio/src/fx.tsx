import { useEffect, useRef, useState } from 'react';
import Lenis from 'lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { useMotion } from './motion';

gsap.registerPlugin(ScrollTrigger);

/* Smooth scrolling, synced with ScrollTrigger. Off when motion is off. */
export function useSmoothScroll() {
  const { motion } = useMotion();
  useEffect(() => {
    if (!motion) return;
    const lenis = new Lenis({ lerp: 0.1, smoothWheel: true });
    lenis.on('scroll', ScrollTrigger.update);
    const tick = (t: number) => lenis.raf(t * 1000);
    gsap.ticker.add(tick);
    gsap.ticker.lagSmoothing(0);
    const onClick = (e: MouseEvent) => {
      const a = (e.target as HTMLElement).closest('a[href^="#"]') as HTMLAnchorElement | null;
      if (!a) return;
      const id = a.getAttribute('href')!;
      const el = id === '#top' ? document.body : document.querySelector(id);
      if (!el) return;
      e.preventDefault();
      lenis.scrollTo(el as HTMLElement, { offset: id === '#top' ? 0 : -70 });
    };
    document.addEventListener('click', onClick);
    (window as unknown as { __lenis?: Lenis }).__lenis = lenis;
    return () => {
      document.removeEventListener('click', onClick);
      gsap.ticker.remove(tick);
      lenis.destroy();
      delete (window as unknown as { __lenis?: Lenis }).__lenis;
    };
  }, [motion]);
}

/* Custom cursor for fine pointers. */
export function Cursor() {
  const { motion } = useMotion();
  const dot = useRef<HTMLDivElement>(null);
  const ring = useRef<HTMLDivElement>(null);
  const [enabled, setEnabled] = useState(false);

  useEffect(() => {
    const fine = window.matchMedia('(pointer: fine)').matches;
    setEnabled(fine && motion);
  }, [motion]);

  useEffect(() => {
    if (!enabled) return;
    document.documentElement.classList.add('has-cursor');
    const pos = { x: innerWidth / 2, y: innerHeight / 2 };
    const r = { x: pos.x, y: pos.y };
    let raf = 0;
    const move = (e: PointerEvent) => {
      pos.x = e.clientX;
      pos.y = e.clientY;
      const t = e.target as HTMLElement;
      ring.current?.classList.toggle('is-link', !!t.closest('a, button, [role="button"], input, select, textarea'));
      ring.current?.classList.toggle('is-view', !!t.closest('[data-cursor="view"]'));
    };
    const loop = () => {
      r.x += (pos.x - r.x) * 0.18;
      r.y += (pos.y - r.y) * 0.18;
      if (dot.current) dot.current.style.transform = `translate3d(${pos.x}px, ${pos.y}px, 0)`;
      if (ring.current) ring.current.style.transform = `translate3d(${r.x}px, ${r.y}px, 0)`;
      raf = requestAnimationFrame(loop);
    };
    window.addEventListener('pointermove', move);
    raf = requestAnimationFrame(loop);
    return () => {
      document.documentElement.classList.remove('has-cursor');
      window.removeEventListener('pointermove', move);
      cancelAnimationFrame(raf);
    };
  }, [enabled]);

  if (!enabled) return null;
  return (
    <>
      <div className="cur-dot" ref={dot} aria-hidden="true" />
      <div className="cur-ring" ref={ring} aria-hidden="true">
        <span>View</span>
      </div>
    </>
  );
}

/* Magnetic effect for elements with [data-magnetic]. */
export function useMagnetic() {
  const { motion } = useMotion();
  useEffect(() => {
    if (!motion || !window.matchMedia('(pointer: fine)').matches) return;
    const els = [...document.querySelectorAll<HTMLElement>('[data-magnetic]')];
    const offs = els.map((el) => {
      const move = (e: PointerEvent) => {
        const r = el.getBoundingClientRect();
        const x = (e.clientX - r.left - r.width / 2) * 0.3;
        const y = (e.clientY - r.top - r.height / 2) * 0.3;
        el.style.transform = `translate3d(${x}px, ${y}px, 0)`;
      };
      const leave = () => (el.style.transform = '');
      el.addEventListener('pointermove', move);
      el.addEventListener('pointerleave', leave);
      return () => {
        el.removeEventListener('pointermove', move);
        el.removeEventListener('pointerleave', leave);
        el.style.transform = '';
      };
    });
    return () => offs.forEach((f) => f());
  }, [motion]);
}

/* Short preloader with a counter; skipped for reduced motion and on repeat visits in a session. */
export function Preloader({ onDone }: { onDone: () => void }) {
  const { motion } = useMotion();
  const [n, setN] = useState(0);
  const [hide, setHide] = useState(false);
  const skip = useRef(false);

  useEffect(() => {
    try {
      skip.current = !motion || sessionStorage.getItem('ms-seen') === '1';
      sessionStorage.setItem('ms-seen', '1');
    } catch {
      skip.current = !motion;
    }
    if (skip.current) {
      setHide(true);
      onDone();
      return;
    }
    const o = { v: 0 };
    const tw = gsap.to(o, {
      v: 100,
      duration: 1.3,
      ease: 'power2.inOut',
      onUpdate: () => setN(Math.round(o.v)),
      onComplete: () => {
        setHide(true);
        onDone();
      },
    });
    return () => {
      tw.kill();
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return (
    <div className={`loader ${hide ? 'is-done' : ''}`} aria-hidden="true">
      <div className="loader__name">Muhammad Saleem</div>
      <div className="loader__count">{n}%</div>
      <div className="loader__bar">
        <span style={{ transform: `scaleX(${n / 100})` }} />
      </div>
    </div>
  );
}

/* Count up a number when it enters the viewport. */
export function useCountUp(target: number, ref: React.RefObject<HTMLElement>) {
  const { motion } = useMotion();
  const [v, setV] = useState(motion ? 0 : target);
  useEffect(() => {
    if (!motion) {
      setV(target);
      return;
    }
    const el = ref.current;
    if (!el) return;
    const o = { v: 0 };
    const st = ScrollTrigger.create({
      trigger: el,
      start: 'top 85%',
      once: true,
      onEnter: () => gsap.to(o, { v: target, duration: 1.6, ease: 'power3.out', onUpdate: () => setV(Math.round(o.v)) }),
    });
    return () => st.kill();
  }, [motion, target, ref]);
  return v;
}
