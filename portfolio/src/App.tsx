import { useState } from 'react';
import Header from './components/Header';
import Hero from './components/Hero';
import Work from './components/Work';
import CaseDialog from './components/CaseDialog';
import Services from './components/Services';
import About from './components/About';
import Packages from './components/Packages';
import Connect from './components/Connect';
import { testimonials } from './content';
import { InquiryProvider, MotionProvider } from './motion';

export default function App() {
  const [caseId, setCaseId] = useState<string | null>(null);
  return (
    <MotionProvider>
      <InquiryProvider>
        <Header />
        <main id="main">
          <Hero />
          <Work onOpen={setCaseId} />
          <Services onOpen={setCaseId} />
          <About />
          {testimonials.length > 0 && (
            <section className="light testi" aria-label="Client testimonials">
              <div className="wrap">
                {testimonials.map((t) => (
                  <figure key={t.name}>
                    <blockquote>“{t.quote}”</blockquote>
                    <figcaption>
                      {t.name} — {t.context}
                    </figcaption>
                  </figure>
                ))}
              </div>
            </section>
          )}
          <Packages />
          <Connect />
        </main>
        <CaseDialog id={caseId} onClose={() => setCaseId(null)} />
      </InquiryProvider>
    </MotionProvider>
  );
}
