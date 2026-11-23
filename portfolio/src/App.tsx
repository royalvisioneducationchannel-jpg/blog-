import { useState } from 'react';
import Header from './components/Header';
import Hero from './components/Hero';
import Work from './components/Work';
import CaseDialog from './components/CaseDialog';
import Services from './components/Services';
import About from './components/About';
import Packages from './components/Packages';
import Process from './components/Process';
import Contact from './components/Contact';
import { profile, testimonials } from './content';
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
            <section className="testimonials section--light" aria-label="Client testimonials">
              <div className="container">
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
          <Process />
          <Contact />
        </main>
        <footer className="footer">
          <div className="container footer__inner">
            <p>
              © {new Date().getFullYear()} {profile.name} · {profile.location}
            </p>
            <p>
              <a href={profile.links.upwork} target="_blank" rel="noopener noreferrer">
                Upwork ↗
              </a>
              {profile.links.linkedin && (
                <a href={profile.links.linkedin} target="_blank" rel="noopener noreferrer">
                  LinkedIn ↗
                </a>
              )}
              <a href="#top">Back to top ↑</a>
            </p>
          </div>
        </footer>
        <CaseDialog id={caseId} onClose={() => setCaseId(null)} />
      </InquiryProvider>
    </MotionProvider>
  );
}
