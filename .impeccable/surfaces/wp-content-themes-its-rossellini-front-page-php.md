---
version: 1
slug: "wp-content-themes-its-rossellini-front-page-php"
primary_target: "wp-content/themes/its-rossellini/front-page.php"
related_targets: []
---

# Home (front page) — ITS Academy Roberto Rossellini

Scope: WordPress front page of the classic theme `its-rossellini`; the same system carries every other template. Visitor mode: Persuade.

Audience and job: an 18–22-year-old (and family) on a phone deciding whether a two-year, publicly funded audiovisual course is worth it, and which one. Action: book a visit (open day or individual meeting) or pre-enrol. Proof on hand: 8 real courses with real photos, the real hero video, published figures (87% employed within 12 months, 1,000 hours, 800+ hours internship, 28 places), the 360 m² sound stage in Via Tiburtina, funding bodies, partner logos. Constraints: logo, institutional palette (blue, deep blue, cyan, electric blue; no yellow/green/red) and Raleway are binding; the current site is the reference to stay faithful to; no invented claims; no content hidden by scroll animation; Polylang IT/EN.

Superseded: the earlier "locandina e fotobusta" direction (yellow second ink, area colours, condensed face) was rejected by the user as not modern and not on-brand.

## Direction contract

THESIS: The current Rossellini site, made cinematic: same institution, palette, Raleway voice and footage, on dark fields where the video is the opening shot. It refuses the light corporate template (card grids, split blocks) and the "film-tech HUD" costume of the current site.

OWN-WORLD: Near-black and deep blue fields with cyan as the single accent; light grey and white only on reading pages. Raleway 900 caps at very large sizes with the italic second line in cyan. Real footage and photographs in rounded frames (14px, 28px for full-width bands). Sections slide over the previous one with 28px rounded shoulders. Pill buttons, circular arrows. No glows, no grids, no mono labels.

STORY: The visitor lands inside the footage, reads "Il futuro si gira adesso." and can act. As the page moves the footage closes into a frame and the eight courses appear as huge titles; hovering one shows its photo at the pointer. Then the open set, the 87% statement and the three steps, the news, the partners, the cyan call to visit, and the institution's name across the footer.

FIRST VIEWPORT: Full-bleed hero video under a deep-blue scrim, transparent header (turns near-black on scroll). Headline across the width at up to ~10.75rem, "si gira" italic cyan; lead bottom-left; cyan "Prenota un incontro" and outline "Scopri i corsi" bottom-right with a round pause control.

FORM: the incumbent itsrossellini.it theme, made cinematic (user-chosen "Cinematografico, scuro" from two overdrive directions; seed: none (user-pinned incumbent world)). Signature moment: the footage closes into a rounded frame on scroll (CSS scroll-driven, static fallback). Signature interaction: the course index preview that follows the pointer (fine pointers only; phones get a snapping carousel; works as plain links without JS).

FINISH: unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance
