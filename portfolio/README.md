# Muhammad Saleem — portfolio

Standalone portfolio site: React 18 + TypeScript + Vite, GSAP ScrollTrigger for scroll storytelling, Geist font.
Minimal editorial style: off-white and dark navy sections, oversized type, a rising glow under the hero,
word-by-word statement reveal, pinned "Selected Works" with marquee project names, stacking service rows,
and a round teal call-to-action that opens the contact form.

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

- All text, project details, forms and links are plain, accessible HTML. The animated hero heading keeps an `aria-label` with the full sentence.
- Animation ownership: GSAP owns the hero letter reveal, the glow and title scroll effects, and the statement word reveal. CSS owns the marquee and card transitions. React state (from ScrollTrigger progress) picks the active project.
- `prefers-reduced-motion` and the header motion toggle stop the marquee, letter reveal and scroll effects. The choice is remembered in localStorage when available.
- Case studies and the contact form open in native `<dialog>` elements (Escape closes them, focus is handled by the browser).
