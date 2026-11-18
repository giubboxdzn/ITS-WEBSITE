# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack

Same stack as the live site itsrossellini.it, by user decision: WordPress (classic PHP theme, no block theme), hand-written CSS and vanilla JavaScript, no framework and no build step. Live server: WordPress ≥ 6.9, PHP 8.4, Apache. Plugins in use on the live site: Polylang (IT/EN), Yoast SEO, Site Kit by Google, Cookie Notice, plus the custom "Rossellini Core" plugin, whose source is not available.

The new theme must read the content model that Rossellini Core registers on the live site, so it can replace the current theme without migrating data:

- `corso` (taxonomy `area_tecnologica`): the eight 2026–2028 courses.
- `edizione_corso` (taxonomies `famiglia`, `biennio`): past and running course editions.
- `evento` (taxonomy `tipo_evento`): open days, guidance sessions, festivals, jams.
- `documento` (taxonomies `tipo`, `biennio`): calls, rankings, interview calls, registers.
- `post` (categories, e.g. "Avvisi"): news.

Most live pages have empty editor content (the old theme/plugin hard-codes them). The new theme renders editor content, so page copy becomes editable in WordPress.

## Users

- **Prospective students** (recent upper-secondary graduates, roughly 18–25) and their families, deciding whether a two-year, post-diploma technical path is worth it compared with university, and which of the eight courses fits. They arrive mostly on phones, often from social media.
- **Candidates and enrolled students** looking for something specific and dated: calls (bandi), interview calls, rankings, deadlines, Erasmus and scholarship notices.
- **Companies, professionals and institutions**: partners, firms offering internships, professionals applying to the teachers/collaborators register, suppliers, and anyone checking the transparency section.

## Product Purpose

Present ITS Academy Roberto Rossellini (Rome) and its publicly funded two-year courses in audiovisual, media and digital technology, turn interest into open-day bookings and pre-enrolments, and publish the institutional documents the Foundation is required to make public. Success: a prospective student understands the offer and books a visit or pre-enrols; a candidate finds the right document in a few seconds.

## Positioning

A non-university tertiary path (ITS Academy, Italian national system) that trains for production jobs by doing production: labs and a 360 m² sound stage in Via Tiburtina, teachers who work in the industry, and more than 800 hours of company internship inside the two years. Courses are publicly funded (FSE+ Lazio, PNRR/NextGenerationEU, national ITS funds).

## Operating Context

- Yearly cycle: open days and individual guidance → pre-enrolment for the next biennium → calls and selection interviews → rankings → courses start. Content peaks around calls and rankings.
- Every course has editions across bienni; documents are organised by course, biennium and edition.
- Bilingual: Italian primary, English mirror under `/en/` (Polylang).
- Contacts: Via Tiburtina 691, Roma (Metro B Pietralata / Monti Tiburtini), Mon–Fri 9:30–13:30 / 14:30–18:30, tel. 06 5576493; separate mailboxes for general office, student office, guidance, Erasmus, placement, administration.

## Capabilities and Constraints

- Course catalogue filterable by technological area (Cinema & TV, Multimedia, Audio, Tech).
- News, events, documents with filters by type and biennium.
- Pre-enrolment and contact flows. Applications are submitted on a separate portal, iscrizioni.itsrossellini.it (a Laravel application on its own server, not WordPress). The theme's pre-enrolment page prepares the application (requirements, steps, help) and is not integrated with the portal (user decision, 18/11/2026). Contacts are e-mail links to the right office.
- Institutional pages: transparency, registers (teachers, suppliers, professionals), privacy and cookie policies.
- Must keep working with Polylang language switching and Yoast SEO output.
- No trackers or external requests added by the theme.

## Brand Commitments

The current itsrossellini.it theme is the visual reference: the new theme stays recognisably the same institution and modernises it. Binding, by user decision:

- **Logo**: the existing monochrome SVG (media-controls mark "pause · record · play" + "ITS ACADEMY / ROBERTO ROSSELLINI" wordmark).
- **Institutional palette, and only it**: blue `#004590`, deep blue `#002c64`, cyan `#49b7e9`, electric blue `#1032cf`, plus near-black and white neutrals. Yellow, green, red or any other hue are not institute colours and must not be introduced, not even as category colours.
- **Typeface**: Raleway (the current site's face), self-hosted.
- **What "modern" means, per the user's references** (three Dribbble shots: a dark biotech site, a light AI-health site, a dark IT-services site; kept locally in `.impeccable/references/`, not committed): content in large rounded containers (the hero is a rounded card holding the footage), a floating pill navigation, light-weight large sentence-case headlines with one emphasised phrase, glass cards floating over imagery, bento tiles mixing figures and photos, image cards with overlapping text cards, partner logos in a white rounded bar, white and blue-gradient pill buttons, light interior pages with a soft blue glow. User decision after review: the home hero is full-bleed, without the outer inset and rounded corners.
- **Fourth reference** (deluxhost.net, a hosting site; screenshot kept locally, not committed), adapted to the school's content on the same base: a single comparison card with check rows (here: what the biennio includes, only published facts), a "where we are" block with info chips and a dotted globe, an FAQ accordion, and a richer footer (columns, socials, the logo as an outlined watermark, an animations switch). Not taken: price tables, "most popular" badges, competitor columns, the grid floor.
- **To move away from** (what makes the current site read as AI-made): monospace micro-labels, "01 / 05" section counters, kickers above headings, grid-line backgrounds, radial glows, outlined giant numbers, the "REC / CH 04" camera frame, coloured stripes on cards, the marquee ticker, 9–10px text.

- **Languages**: Italian (default) and English, as on the live site (Polylang); the visitor switches with a flag selector in the header and the footer.

## Evidence on Hand

- Published figures (site-wide): 87% of graduates employed within 12 months ("dato pubblicato da ITS Rossellini"), 1,000 hours of classes and labs, 800+ hours of internship, 2 years, 28 places per course, Foundation approved by Regione Lazio in December 2009.
- Real content on the live site: 8 courses with skills, roles, programme and tools; 60+ news posts; 20+ events; 100+ documents; partner logos (Orange Media Group, UNINT, Università Roma Tre, Cine TV Roberto Rossellini, Città Metropolitana di Roma Capitale, institutional funding strip).
- Photography: real lab/set photos exist as featured images on the live site.
- Absent, never to be fabricated: student testimonials, named alumni outcomes, company names beyond the published partners, rankings or awards not already published.

## Product Principles

1. The decision comes first: what the course is, what job it leads to, what it costs (publicly funded), when the next step is.
2. Dated, official things must be findable in seconds and never look like marketing.
3. Show the real place and real work (sets, labs, students) instead of decoration standing in for it.
4. The same information has the same shape in Italian and English.

## Accessibility & Inclusion

Public-funded educational institution: target WCAG 2.2 AA (Italian public-sector expectations), readable on low-end phones, respect `prefers-reduced-motion`, no content hidden behind scroll animations. Legal obligations (Legge 4/2004, the accessibility statement on form.agid.gov.it) are in `ACCESSIBILITA.md`.

## Decisions

Key decisions, in order; dates from the project history.

| Date | Decision |
|---|---|
| 16/11/2026 | Same stack as the live site: classic WordPress theme, hand-written CSS and JavaScript, no framework or build. |
| 16/11/2026 | The first direction ("locandina e fotobusta": yellow second ink, area colours, condensed face) is rejected as not modern and not on-brand; the theme follows the current site, modernised. |
| 16/11/2026 | Visual references: three Dribbble shots, then deluxhost.net for the comparison card, the place block, the FAQ and the footer. |
| 16/11/2026 | Home hero and footer run edge to edge, without inset or corners. |
| 16/11/2026 | Motion only behind the footer switch "Animazioni"; flipping it reloads the page from the top. |
| 16/11/2026 | Italian and English, with flags in the header and the footer, as on the live site (Polylang). |
| 17/11/2026 | Every interior page on white; the navy floating header on every page. |
| 17/11/2026 | Funding bar built only from the official vector logos (sources in `assets/img/funding/SOURCES.md`). |
| 18/11/2026 | Scroll animations are reversible: going back up, things go out in view with the same animation in reverse, and play again on the way down. |
| 18/11/2026 | The enrolment portal (iscrizioni.itsrossellini.it) is not integrated into the site. |
| 18/11/2026 | Every published figure is edited in the Customizer, the sound stage area (360 m²) included: none is written in the code. |
