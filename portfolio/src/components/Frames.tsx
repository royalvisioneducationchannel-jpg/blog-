import type { Shot } from '../content';

export function BrowserFrame({ shot, url, eager = false, className = '' }: { shot: Shot; url?: string; eager?: boolean; className?: string }) {
  return (
    <figure className={`browser ${className}`}>
      <div className="browser__bar" aria-hidden="true">
        <i />
        <i />
        <i />
        {url && <span className="browser__url">{url.replace(/^https?:\/\//, '').replace(/\/$/, '')}</span>}
      </div>
      <img src={shot.src} width={shot.w} height={shot.h} alt={shot.alt} loading={eager ? 'eager' : 'lazy'} decoding="async" />
    </figure>
  );
}

export function PhoneFrame({ shot, eager = false, className = '' }: { shot: Shot; eager?: boolean; className?: string }) {
  return (
    <figure className={`phone ${className}`}>
      <div className="phone__screen">
        <img src={shot.src} width={shot.w} height={shot.h} alt={shot.alt} loading={eager ? 'eager' : 'lazy'} decoding="async" />
      </div>
    </figure>
  );
}

export function Card({ shot, className = '' }: { shot: Shot; className?: string }) {
  return (
    <figure className={`card-shot ${className}`}>
      <img src={shot.src} width={shot.w} height={shot.h} alt={shot.alt} loading="lazy" decoding="async" />
    </figure>
  );
}
