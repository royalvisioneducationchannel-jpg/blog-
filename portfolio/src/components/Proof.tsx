import { useRef } from 'react';
import { marquee, stats } from '../content';
import { useCountUp } from '../fx';

function Stat({ s }: { s: (typeof stats)[number] }) {
  const ref = useRef<HTMLParagraphElement>(null);
  const v = useCountUp(s.value, ref);
  return (
    <div className="stat">
      <p className="stat__v" ref={ref}>
        <span className="sr-only">
          {s.prefix}
          {s.value.toLocaleString('en-US')}
          {s.suffix}
        </span>
        <span aria-hidden="true">
          {s.prefix}
          {v.toLocaleString('en-US')}
          {s.suffix}
        </span>
      </p>
      <p className="stat__l">{s.label}</p>
    </div>
  );
}

export default function Proof() {
  const items = [...marquee, ...marquee];
  return (
    <section className="proof" aria-label="Skills and results">
      <div className="band" aria-hidden="true">
        <div className="band__track">
          {items.map((m, i) => (
            <span key={i}>
              {m} <i>✦</i>
            </span>
          ))}
        </div>
      </div>
      <div className="stats wrap">
        {stats.map((s) => (
          <Stat key={s.label} s={s} />
        ))}
      </div>
      <p className="stats__note wrap">Ad figures are lifetime data from Meta Ads Manager for a study-abroad consultancy.</p>
    </section>
  );
}
