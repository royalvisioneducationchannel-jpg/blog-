import { useState } from 'react';
import Header from './components/Header';
import Hero from './components/Hero';
import Proof from './components/Proof';
import Work from './components/Work';
import CaseDialog from './components/CaseDialog';
import Services from './components/Services';
import About from './components/About';
import Pricing from './components/Pricing';
import Connect from './components/Connect';
import { testimonials } from './content';
import { InquiryProvider, MotionProvider } from './motion';
import { Cursor, Preloader, useMagnetic, useSmoothScroll } from './fx';

function Site() {
  const [caseId, setCaseId] = useState<string | null>(null);
  const [ready, setReady] = useState(false);
  useSmoothScroll();
  useMagnetic();
  return (
    <>
      <Preloader onDone={() => setReady(true)} />
      <Cursor />
      <Header />
      <main id="main">
        <Hero ready={ready} />
        <Proof />
        <Work onOpen={setCaseId} />
        <Services onOpen={setCaseId} />
        <About />
        {testimonials.length > 0 && (
          <section className="testi wrap" aria-label="Client testimonials">
            {testimonials.map((t) => (
              <figure key={t.name}>
                <blockquote>“{t.quote}”</blockquote>
                <figcaption>
                  {t.name} — {t.context}
                </figcaption>
              </figure>
            ))}
          </section>
        )}
        <Pricing />
      </main>
      <Connect />
      <CaseDialog id={caseId} onClose={() => setCaseId(null)} />
    </>
  );
}

export default function App() {
  return (
    <MotionProvider>
      <InquiryProvider>
        <Site />
      </InquiryProvider>
    </MotionProvider>
  );
}
