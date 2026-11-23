/*
 * All editable site content lives here.
 * Only verified facts are published. Fields marked TODO are draft
 * placeholders: empty values are hidden on the site until you fill them in.
 */

export const profile = {
  name: 'Muhammad Saleem',
  role: 'WordPress & Elementor Developer · Meta Ads Specialist',
  location: 'Faisalabad, Pakistan',
  languages: ['English', 'Urdu', 'Punjabi'],
  portrait: './img/portrait-cutout.webp',
  links: {
    upwork: 'https://www.upwork.com/freelancers/~01d6e2c0380c59ed62',
    linkedin: '', // TODO: add your public LinkedIn profile URL
    email: '', // TODO: add the public email address you want clients to use
    whatsapp: '', // TODO: optional, e.g. https://wa.me/923001234567
  },
  // TODO: optional form backend (e.g. a Formspree endpoint). Empty = email/Upwork fallback.
  formEndpoint: '',
};

export const hero = {
  greeting: 'Hi, I’m Saleem',
  lineA: ['Websites that', 'stand out.'],
  lineB: ['Campaigns built', 'with purpose.'],
  lead: 'I design and build WordPress & Elementor websites and WooCommerce stores, and run Meta ads on Facebook and Instagram that bring real enquiries.',
  chips: ['WordPress', 'Elementor', 'WooCommerce', 'Meta Ads', 'Meta Pixel'],
};

/* Verified numbers only (Meta Ads Manager lifetime data + live projects on this site). */
export const stats = [
  { value: 6521, prefix: '', suffix: '', label: 'Messaging conversations from Meta campaigns' },
  { value: 951, prefix: '', suffix: 'K', label: 'People reached on Facebook & Instagram' },
  { value: 3, prefix: '', suffix: '', label: 'Live websites in this portfolio' },
  { value: 65, prefix: 'PKR ', suffix: '', label: 'Best cost per conversation' },
];

export const marquee = ['WordPress', 'Elementor Pro', 'WooCommerce', 'Meta Ads', 'Meta Pixel', 'Landing Pages', 'Photoshop', 'Illustrator', 'Rank Math SEO'];

export type Shot = { src: string; w: number; h: number; alt: string };

export type Project = {
  id: string;
  title: string;
  category: string;
  brief: string;
  role: string;
  delivered: string[];
  tools: string[];
  outcome: string;
  outcomeVerified: boolean;
  live?: string;
  desktop?: Shot;
  mobile?: Shot;
  creative?: Shot[];
  decisions: string[];
  service: string;
  tags: string[];
};

export const projects: Project[] = [
  {
    id: 'royal-vision-store',
    title: 'Royal Vision Store',
    category: 'E-commerce · WooCommerce store',
    brief:
      'An online electronics and lifestyle store in Pakistan needed a fast, mobile-friendly shop where customers can order with cash on delivery, and where the team can process orders and promote products with ads.',
    role: 'WordPress & WooCommerce build, store design and integrations',
    delivered: [
      'WooCommerce store with custom product pages',
      'One-page “Order Now” checkout',
      'Courier integration and printable order slips',
      'Meta and Google product feeds for catalog ads',
      'On-page SEO setup with Rank Math and caching',
    ],
    tools: ['WordPress', 'WooCommerce', 'Elementor', 'Rank Math', 'LiteSpeed Cache', 'Meta for WooCommerce'],
    outcome:
      'Delivered a live store that takes orders nationwide. The one-page checkout was designed to shorten the path from product page to order on mobile.',
    outcomeVerified: false,
    live: 'https://royalvision.online/',
    desktop: { src: './img/store-desktop.webp', w: 1280, h: 900, alt: 'Royal Vision Store homepage with category menu and featured earbuds offer' },
    decisions: [
      'Category icons in the header so shoppers reach products in one tap.',
      'Price, delivery and cash-on-delivery information placed in the first screen to build trust.',
      'Product feeds connected so the same catalog can power Meta and Google shopping ads.',
    ],
    service: 'WooCommerce Stores',
    tags: ['WooCommerce', 'Elementor', 'Integrations'],
  },
  {
    id: 'royal-vision-education',
    title: 'Royal Vision Education',
    category: 'Study-abroad consultancy · Lead generation website',
    brief:
      'A study-abroad consultancy with several offices in Pakistan needed a website that explains destinations and the visa journey, and turns visitors into assessment requests.',
    role: 'WordPress & Elementor website design and build',
    delivered: [
      'Destination pages (UK, Australia, Cyprus, Japan, USA and more)',
      'Multi-step study-abroad eligibility checker',
      '“Counseling to visa” student journey timeline',
      'Free assessment lead form with WhatsApp hand-off',
      'Responsive layout for mobile visitors',
    ],
    tools: ['WordPress', 'Elementor Pro', 'LiteSpeed Cache', 'Site Kit by Google'],
    outcome:
      'Delivered a lead-focused website. The eligibility checker and assessment form were built to make it easier for students to start a conversation with a counselor.',
    outcomeVerified: false,
    live: 'https://www.royalvision.com.pk/new/',
    desktop: { src: './img/edu-desktop.webp', w: 1280, h: 800, alt: 'Royal Vision Education homepage: Study Abroad Consultants in Pakistan with destination cards' },
    mobile: { src: './img/edu-mobile.webp', w: 390, h: 664, alt: 'Royal Vision Education homepage on a phone' },
    decisions: [
      'Destination cards arranged around one clear call to action instead of a long list.',
      'Eligibility checker gives visitors a quick, useful first step before contacting the team.',
      'Student journey shown as a timeline so the process feels clear and trustworthy.',
    ],
    service: 'Business Websites & Landing Pages',
    tags: ['Elementor Pro', 'Lead Gen', 'Forms'],
  },
  {
    id: 'royal-vision-law',
    title: 'Royal Vision Law Associate',
    category: 'Law firm · Professional business website',
    brief:
      'A firm of advocates with offices in Lahore and Faisalabad needed a calm, credible website that explains its practice areas and team, and makes it easy to request a consultation.',
    role: 'Website design and WordPress/Elementor build',
    delivered: [
      'Home, Our Team, Insights and Contact pages',
      'The Firm and Practice Areas sections',
      'Consultation calls to action across the site',
      'Responsive desktop and mobile layouts',
    ],
    tools: ['WordPress', 'Elementor', 'PRO Elements', 'LiteSpeed Cache'],
    outcome:
      'Delivered a complete firm website. The design uses restrained typography and clear calls to action so visitors understand the firm quickly and know how to get in touch.',
    outcomeVerified: false,
    live: 'https://royalvisionlaw.com/',
    desktop: { src: './img/law-desktop.webp', w: 1280, h: 900, alt: 'Royal Vision Law Associate homepage: Clear advice. Considered action.' },
    mobile: { src: './img/law-mobile.webp', w: 390, h: 844, alt: 'Royal Vision Law Associate homepage on a phone' },
    decisions: [
      'A serif headline and deep green palette to feel established and trustworthy.',
      'Registration details in the top bar so credibility is visible immediately.',
      'Primary action “Discuss your matter” repeated where visitors are ready to decide.',
    ],
    service: 'Business Websites & Landing Pages',
    tags: ['Web Design', 'Elementor', 'Responsive'],
  },
  {
    id: 'meta-ads-study-abroad',
    title: 'Meta Ads for a Study-Abroad Consultancy',
    category: 'Lead generation · Facebook & Instagram ads',
    brief:
      'A study-abroad consultancy in Pakistan wanted a steady flow of student enquiries through Facebook and Instagram messaging and lead campaigns.',
    role: 'Campaign setup, audience targeting and reporting',
    delivered: [
      'Messaging and lead campaigns for several study destinations',
      'Multiple campaigns compared on cost per conversation',
      'Reporting on spend, reach, clicks and conversations',
    ],
    tools: ['Meta Ads Manager', 'Facebook', 'Instagram', 'Messenger'],
    outcome:
      'Lifetime account data from Meta Ads Manager: 6,521 messaging conversations, 951K people reached, 2.38M impressions and 27.5K link clicks at an average of PKR 158 per conversation. The best campaign brought 505 conversations at about PKR 65 each.',
    outcomeVerified: true,
    creative: [
      { src: './img/meta-results.webp', w: 1200, h: 900, alt: 'Meta Ads results summary: 6,521 conversations, 951K reach, 2.38M impressions' },
      { src: './img/meta-testing.webp', w: 1200, h: 900, alt: 'Cost per conversation by campaign, best campaign PKR 65 for 505 conversations' },
    ],
    decisions: [
      'Messaging objectives so students could start a conversation directly from the ad.',
      'Separate campaigns by destination to compare cost per conversation.',
      'Cost per conversation used as the main success metric, because it reflects real enquiries.',
    ],
    service: 'Meta Ads Campaigns',
    tags: ['Meta Ads', 'Lead Gen', 'Reporting'],
  },
];

export type Service = {
  title: string;
  who: string;
  deliver: string[];
  receive: string;
  example?: string; // project id
  thumb: string;
};

export const services: Service[] = [
  {
    title: 'Business Websites & Landing Pages',
    who: 'Service businesses, consultants and firms that need a credible website or a focused page for one offer.',
    deliver: ['Custom WordPress & Elementor design', 'Responsive desktop, tablet and mobile layouts', 'Contact and lead forms', 'Basic on-page SEO setup'],
    receive: 'A website you can edit yourself in Elementor, tested on mobile, tablet and desktop.',
    example: 'royal-vision-law',
    thumb: './img/law-desktop.webp',
  },
  {
    title: 'WooCommerce Stores',
    who: 'Shops that want to sell online with a smooth checkout and easy order handling.',
    deliver: ['Store setup and product pages', 'Checkout, payment and shipping setup', 'Courier and order-slip workflows', 'Meta & Google product feeds'],
    receive: 'A working online store, ready for orders and catalog ads.',
    example: 'royal-vision-store',
    thumb: './img/store-desktop.webp',
  },
  {
    title: 'Meta Ads Campaigns',
    who: 'Businesses that want leads, messages or sales from Facebook and Instagram.',
    deliver: ['Campaign setup in your own ad account', 'Audience targeting and ad copy', 'Meta Pixel and event checks', 'Weekly reports with honest numbers'],
    receive: 'Campaigns you own and control, previews before launch, and clear reporting.',
    example: 'meta-ads-study-abroad',
    thumb: './img/meta-results.webp',
  },
  {
    title: 'Fixes, Tracking & Creatives',
    who: 'Existing websites with layout bugs, broken forms or missing tracking, and brands that need ad visuals.',
    deliver: ['Elementor & mobile layout fixes', 'Contact form and email (SMTP) fixes', 'Meta Pixel & Conversions API setup', 'Ad creatives and social posts in Photoshop & Illustrator'],
    receive: 'A backup before any change and a short report of what was fixed.',
    thumb: './img/meta-cover.webp',
  },
];

export const skillGroups = [
  { title: 'Website Design & Development', items: ['WordPress', 'Elementor & Elementor Pro', 'WooCommerce', 'Landing pages', 'Responsive design', 'Speed optimization', 'HTML, CSS & PHP'] },
  { title: 'Advertising & Lead Generation', items: ['Meta Ads Manager', 'Facebook & Instagram ads', 'Lead & messaging campaigns', 'Audience targeting', 'Campaign reporting'] },
  { title: 'Creative Tools', items: ['Adobe Photoshop', 'Adobe Illustrator', 'Ad creatives', 'Social media design'] },
  { title: 'Integrations & Tracking', items: ['Meta Pixel & Conversions API', 'Meta & Google product feeds', 'Rank Math SEO', 'Contact forms & SMTP', 'Courier integrations'] },
];

export type Package = {
  id: string;
  name: string;
  forWho: string;
  deliverables: string[];
  limits?: string;
  revisions?: string;
  delivery?: string;
  price?: string; // empty = "Request a Quote"
  note?: string;
};

/*
 * Prices below come from your live Upwork Project Catalog.
 * "Landing Page" and "Website + Campaign Setup" scopes are DRAFT proposals:
 * no price/delivery is shown until you confirm them.
 */
export const packages: Package[] = [
  {
    id: 'landing-page',
    name: 'Landing Page',
    forWho: 'One service, offer or campaign that needs a focused page.',
    deliverables: ['One custom landing page', 'Responsive layout', 'Contact or lead form', 'Call-to-action links (WhatsApp, booking, etc.)'],
    limits: '1 page',
  },
  {
    id: 'business-website',
    name: 'Business Website',
    forWho: 'Businesses and firms that need a complete, professional online presence.',
    deliverables: ['An agreed number of pages', 'Responsive design', 'Service and project sections', 'Contact integrations', 'Basic on-page SEO setup'],
    limits: 'Page count agreed before we start',
    price: 'From $100',
    note: 'Starting price from my Upwork Project Catalog. Final price depends on pages and features.',
  },
  {
    id: 'website-campaign',
    name: 'Website + Campaign Setup',
    forWho: 'Businesses that need a website or landing page and a Meta advertising foundation.',
    deliverables: ['Agreed website or landing-page scope', 'Meta campaign setup', 'Agreed ad creative deliverables', 'Pixel / tracking review where supported'],
    note: 'Ad spend is paid by you directly to Meta and is not part of the service fee.',
  },
];

export const metaAdsTiers = [
  { name: 'Starter', price: '$40', items: ['1 campaign, 1 audience', '2 ads + copy', 'Pixel check', '2-day delivery · 1 revision'] },
  { name: 'Standard', price: '$80', items: ['1 campaign, 2 audiences', '4 ads + copy', '7 days optimization + weekly report', '3-day delivery · 2 revisions'] },
  { name: 'Advanced', price: '$150', items: ['2 campaigns, 3 audiences', '6 ads + copy, Pixel events setup', '14 days management + 2 reports', '4-day delivery · 3 revisions'] },
];

export const quickServices = [
  { name: 'Elementor & WordPress layout and mobile fixes', price: 'From $15' },
  { name: 'Meta Pixel & Conversions API setup', price: 'From $25' },
  { name: 'WordPress bug fixes & WooCommerce issues', price: 'From $25' },
];

export const process = [
  { n: '01', title: 'Understand', text: 'We discuss your business, audience, goals and scope, and agree on a clear plan and price.' },
  { n: '02', title: 'Design', text: 'I develop the visual direction and key layouts, and share them with you before building.' },
  { n: '03', title: 'Build', text: 'I implement the approved design and integrations, with a full backup before any change on live sites.' },
  { n: '04', title: 'Review & Launch', text: 'I test on mobile, tablet and desktop, make the agreed refinements, and prepare the launch.' },
];

// TODO: add real client testimonials (exact wording, with permission). Section is hidden while empty.
export const testimonials: { quote: string; name: string; context: string }[] = [];

export const projectTypes = ['Business website', 'Landing page', 'WooCommerce store', 'Meta ads campaign', 'Website + campaign', 'Fix or improvement', 'Other'];
export const budgets = ['Not sure yet', 'Under $100', '$100 – $300', '$300 – $700', '$700+'];
