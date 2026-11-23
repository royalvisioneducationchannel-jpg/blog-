# Muhammad Saleem — portfolio

Standalone portfolio site: React 18 + TypeScript + Vite, Three.js via React Three Fiber (lazy-loaded shader hero),
GSAP ScrollTrigger and Lenis smooth scrolling. Geist with Instrument Serif italic accents.

Design: dark midnight canvas with a cobalt accent and one ivory section (About). Preloader, custom cursor,
magnetic buttons, interactive 3D shader blob in the hero, skills marquee with verified counters, stacking
project cards, spotlight service cards, pricing tabs (Websites / Meta Ads / Quick fixes) and a closing CTA
that opens the contact form.

## Run

```bash
npm install
npm run dev       # local development
npm run build     # production build in dist/
npm run preview   # serve the production build
```

`dist/` is a static site. Upload its contents to any static host (Netlify, Vercel, Hostinger, cPanel). It uses relative paths, so it also works in a sub-folder.

## Editing content

Everything is in `src/content.ts`: profile, links, projects, services, skills, packages, Meta Ads tiers, process and testimonials. Images are in `public/img/`.

## Missing information (fill in `src/content.ts`)

| Item | Status |
|---|---|
| Public email | Empty. The form falls back to copying the brief and sending the client to Upwork. |
| LinkedIn URL | Added (www.linkedin.com/in/saleem-chishti). |
| WhatsApp link | Optional; hidden while empty. |
| Form backend | Empty `formEndpoint`. Add e.g. a Formspree endpoint to send inquiries directly. A success message is shown only when the endpoint returns OK. |
| Testimonials | None yet (Upwork has 0 reviews). The section is hidden while the list is empty. |
| Landing Page and Website + Campaign packages | Draft scopes with no price or delivery time. They show "Request a Quote" until confirmed. |
| Meta Ads case study | Uses lifetime Meta Ads Manager data from the study-abroad ad account. Confirm you managed these campaigns before publishing. |
| Royal Vision Store mobile screenshot | Not captured (rate-limited); the store project shows desktop only. |

## Implementation notes

- All text, project details, forms and links are plain, accessible HTML. The animated hero heading keeps an `aria-label` with the full sentence.
- Animation ownership: GSAP owns the hero line reveal, the hero copy and project-card scroll effects and the counters; the R3F frame loop owns the 3D blob; CSS owns the marquee, floating cards and hover states; a small rAF loop owns the custom cursor.
- The 3D scene is lazy-loaded, renders only while the hero is visible, and falls back to a CSS gradient orb without WebGL.
- `prefers-reduced-motion` and the header motion toggle turn off smooth scrolling, the preloader, custom cursor, marquee, 3D idle motion and scroll effects. The choice is remembered in localStorage when available.
- Case studies and the contact form open in native `<dialog>` elements (Escape closes them, focus is handled by the browser).
