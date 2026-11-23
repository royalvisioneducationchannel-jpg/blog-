# Floating Digital Studio — Muhammad Saleem portfolio

Standalone portfolio site: React 18 + TypeScript + Vite, Three.js via React Three Fiber (hero scene), GSAP ScrollTrigger (intro and scroll storytelling).

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
| LinkedIn URL | Empty; the link is hidden until added. |
| WhatsApp link | Optional; hidden while empty. |
| Form backend | Empty `formEndpoint`. Add e.g. a Formspree endpoint to send inquiries directly. A success message is shown only when the endpoint returns OK. |
| Testimonials | None yet (Upwork has 0 reviews). The section is hidden while the list is empty. |
| Landing Page and Website + Campaign packages | Draft scopes with no price or delivery time. They show "Request a Quote" until confirmed. |
| Meta Ads case study | Uses lifetime Meta Ads Manager data from the study-abroad ad account. Confirm you managed these campaigns before publishing. |
| Royal Vision Store mobile screenshot | Not captured (rate-limited); the store project shows desktop only. |

## Implementation notes

- Text, project details, forms and links are plain HTML, outside WebGL.
- The 3D scene is lazy-loaded after first paint. It renders only while the hero is on screen and the tab is visible, and caps device pixel ratio (lower on low-core devices).
- A static composition of the same previews is shown first. It stays as the fallback when WebGL is unavailable or the scene fails.
- `prefers-reduced-motion` and the header motion toggle stop decorative floating, tilt and intro animation. The choice is remembered in localStorage when available.
- Animation ownership: GSAP owns text reveal and the intro/scroll values; R3F `useFrame` owns 3D transforms; CSS owns DOM float keyframes; CSS variables (`--px`, `--py`, `--away`) drive DOM tilt and parallax wrappers.
