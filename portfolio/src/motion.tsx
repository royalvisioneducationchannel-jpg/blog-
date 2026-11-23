import { createContext, useContext, useEffect, useState, type ReactNode } from 'react';

type MotionCtx = { motion: boolean; reduced: boolean; toggle: () => void };
const Ctx = createContext<MotionCtx>({ motion: true, reduced: false, toggle: () => {} });

const KEY = 'ms-motion';

function readStored(): boolean | null {
  try {
    const v = localStorage.getItem(KEY);
    return v === null ? null : v === '1';
  } catch {
    return null;
  }
}

export function MotionProvider({ children }: { children: ReactNode }) {
  const mq = typeof window !== 'undefined' ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;
  const [reduced, setReduced] = useState(() => !!mq?.matches);
  const [motion, setMotion] = useState(() => readStored() ?? !mq?.matches);

  useEffect(() => {
    if (!mq) return;
    const on = (e: MediaQueryListEvent) => {
      setReduced(e.matches);
      if (readStored() === null) setMotion(!e.matches);
    };
    mq.addEventListener('change', on);
    return () => mq.removeEventListener('change', on);
  }, [mq]);

  useEffect(() => {
    document.documentElement.classList.toggle('motion-off', !motion);
  }, [motion]);

  const toggle = () =>
    setMotion((m) => {
      try {
        localStorage.setItem(KEY, m ? '0' : '1');
      } catch {
        /* storage unavailable: keep in-memory only */
      }
      return !m;
    });

  return <Ctx.Provider value={{ motion, reduced, toggle }}>{children}</Ctx.Provider>;
}

export const useMotion = () => useContext(Ctx);

export function useMediaQuery(q: string) {
  const [m, setM] = useState(() => (typeof window !== 'undefined' ? window.matchMedia(q).matches : false));
  useEffect(() => {
    const mq = window.matchMedia(q);
    const on = () => setM(mq.matches);
    on();
    mq.addEventListener('change', on);
    return () => mq.removeEventListener('change', on);
  }, [q]);
  return m;
}

/* Shared inquiry state: package/service selections prefill the contact form dialog. */
type Inquiry = { projectType?: string; pkg?: string };
type InquiryCtx = { inquiry: Inquiry; open: boolean; choose: (i?: Inquiry) => void; close: () => void };
const ICtx = createContext<InquiryCtx>({ inquiry: {}, open: false, choose: () => {}, close: () => {} });

export function InquiryProvider({ children }: { children: ReactNode }) {
  const [inquiry, setInquiry] = useState<Inquiry>({});
  const [open, setOpen] = useState(false);
  const choose = (i: Inquiry = {}) => {
    setInquiry(i);
    setOpen(true);
  };
  return <ICtx.Provider value={{ inquiry, open, choose, close: () => setOpen(false) }}>{children}</ICtx.Provider>;
}

export const useInquiry = () => useContext(ICtx);
