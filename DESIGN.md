---
name: ITS Academy Roberto Rossellini
description: Rounded panels on a deep-navy canvas, glass over real footage, light Raleway display — in the institute's own blues and cyan.
colors:
  blu: "#004590"
  blu-notte: "#002c64"
  ciano: "#49b7e9"
  elettrico: "#1032cf"
  abisso: "#04152b"
  abisso-2: "#0a2140"
  abisso-3: "#112c52"
  bianco: "#ffffff"
  chiaro: "#f4f7fb"
  inchiostro: "#0d1b2e"
  muted: "#55627a"
  muted-dark: "#a9b8cf"
  bordo: "#e3e9f1"
typography:
  hero:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(3rem, 7.6vw, 7.25rem)"
    fontWeight: 300
    lineHeight: 0.98
    letterSpacing: "-0.045em"
  display:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(2.3rem, 4.8vw, 4.25rem)"
    fontWeight: 300
    lineHeight: 1.04
    letterSpacing: "-0.035em"
  display-m:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(2rem, 3.4vw, 3rem)"
    fontWeight: 300
    lineHeight: 1.04
    letterSpacing: "-0.035em"
  page-title:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(2.5rem, 5.6vw, 4.75rem)"
    fontWeight: 300
    lineHeight: 1.02
    letterSpacing: "-0.04em"
  stat:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(2rem, 3.2vw, 2.9rem)"
    fontWeight: 300
    lineHeight: 1
    letterSpacing: "-0.04em"
  card-title:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(1.2rem, 1.5vw, 1.4rem)"
    fontWeight: 600
    lineHeight: 1.15
    letterSpacing: "-0.02em"
  body:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "1.0625rem"
    fontWeight: 400
    lineHeight: 1.6
  lead:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(1.0625rem, 1.3vw, 1.2rem)"
    fontWeight: 400
    lineHeight: 1.55
  ui:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "0.9375rem"
    fontWeight: 600
    lineHeight: 1.1
  label:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "0.8125rem"
    fontWeight: 600
    letterSpacing: "0.06em"
rounded:
  sm: "14px"
  md: "22px"
  lg: "30px"
  xl: "40px"
  pill: "999px"
spacing:
  gutter: "clamp(1rem, 4vw, 3.5rem)"
  inset: "clamp(0.5rem, 1.2vw, 1rem)"
  section: "clamp(4rem, 8vw, 7rem)"
  wrap: "1320px"
components:
  button-white:
    backgroundColor: "{colors.bianco}"
    textColor: "{colors.inchiostro}"
    typography: "{typography.ui}"
    rounded: "{rounded.pill}"
    padding: "0.7rem 1.4rem"
    height: "3rem"
  button-grad:
    backgroundColor: "{colors.elettrico}"
    textColor: "{colors.bianco}"
    typography: "{typography.ui}"
    rounded: "{rounded.pill}"
    padding: "0.7rem 1.4rem"
    height: "3rem"
  button-outline:
    backgroundColor: "{colors.bianco}"
    textColor: "{colors.inchiostro}"
    rounded: "{rounded.pill}"
    height: "3rem"
  tile:
    backgroundColor: "{colors.abisso-2}"
    textColor: "{colors.bianco}"
    rounded: "{rounded.lg}"
  panel:
    backgroundColor: "{colors.bianco}"
    textColor: "{colors.inchiostro}"
    rounded: "{rounded.xl}"
  chip:
    backgroundColor: "{colors.chiaro}"
    textColor: "{colors.blu}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
  input:
    backgroundColor: "{colors.bianco}"
    textColor: "{colors.inchiostro}"
    rounded: "{rounded.pill}"
    height: "3rem"
    padding: "0 1.1rem"
---

# Design System: ITS Academy Roberto Rossellini

## Overview

**Creative North Star: "La scuola, in una card"**

The direction comes from three references the user chose (a dark biotech site, a light AI-health site, a dark IT-services site), translated into the institute's palette and Raleway; a fourth (a hosting site) adds the comparison card, the place block with the globe, the FAQ accordion and the fuller footer. Content lives in large rounded containers: the home hero runs the real footage edge to edge (the user dropped its outer inset and corners), light sections are white panels inset into a deep-navy canvas, figures and photos sit in a bento of tiles. Type is light and large, in sentence case, with one phrase emphasised. Glass is used only where something floats over imagery: the navigation pill, the stat cards on the hero, the chips on photos.

Interior pages (courses, news, documents) are light: white ground with a soft blue glow at the top, the same pills, radii and type.

**Key Characteristics:**
- Deep-navy canvas made of tones of the institutional blue; white panels inset by 8–16px with 40px corners.
- Raleway 300 display, sentence case, one phrase at 600 in cyan (dark) or electric blue (light).
- Glass pills and cards over footage and photos.
- Bento tiles: a CTA tile, stat tiles (label + number), photo tiles, a list tile, one gradient accent tile.
- Pill buttons; the arrow icon rotates to ↗ on hover.
- Photo cards with a bottom scrim, a glass area chip and a white circular arrow.

## Colors

### Primary
- **Abisso** (`#04152b`), **Abisso 2** (`#0a2140`), **Abisso 3** (`#112c52`): darker tones of the institutional deep blue. Home canvas, tiles, footer.
- **Blu** (`#004590`) and **Blu notte** (`#002c64`): links, chips, gradient band, text on light accents.

### Secondary
- **Ciano** (`#49b7e9`): emphasis on dark grounds (headline phrase, icons), list dots, glow.
- **Elettrico** (`#1032cf`): emphasis on light grounds, primary gradient start, focus ring on light.
- **Gradients** (institutional only): buttons `#1032cf → #2a6fe0`; accent tile and final call `#1032cf → #004590 → #155fae`.

### Neutral
- **Bianco**, **Chiaro** (`#f4f7fb`), **Inchiostro** (`#0d1b2e`), **Muted** (`#55627a`), **Muted dark** (`#a9b8cf`), **Bordo** (`#e3e9f1`).

### Named Rules
**The Institute Colours Only Rule.** Blues, cyan and their tones only. No yellow, green, red or category colours; areas are named, not coloured.

**The Readable Gradient Rule.** White text sits only on the darker part of the institutional gradients; the cyan end never carries small white text.

## Typography

**Font:** Raleway (variable, normal + italic, self-hosted, OFL), `font-variant-numeric: lining-nums`.

### Hierarchy
- **Hero** (300, clamp 3–7.25rem, 0.98, −0.045em): home headline; the emphasised phrase is 600 cyan and never breaks across lines.
- **Display** (300, clamp 2.3–4.25rem, 1.04) and **Display M** (clamp 2–3rem): section headings with one `<strong>` phrase.
- **Page title** (300, clamp 2.5–4.75rem): interior h1; long titles step down to clamp 2.1–3.5rem.
- **Stat** (300, clamp 2–2.9rem): numbers in tiles and glass cards, with an uppercase **Label** (600, 0.8125rem, +0.06em) above.
- **Card title** (600, clamp 1.2–1.4rem); **Body** (400, 1.0625rem, 1.6, 50ch); **Lead** (400, clamp 1.0625–1.2rem); **UI** (600, 0.9375rem).

### Named Rules
**The Light Display Rule.** Headlines are light (300) and in sentence case; weight is spent on one phrase only.

**The No Kicker Rule.** No badge or label above a headline. Pills and chips go on imagery, beside actions, or after the title.

## Layout

- Canvas inset `clamp(0.5rem, 1.2vw, 1rem)`; container 1320px; section rhythm `clamp(4rem, 8vw, 7rem)`.
- Home: full-bleed hero (full viewport, no inset or corners) → partner bar overlapping its lower edge → bento on canvas → courses in a white panel → comparison card on canvas → image with overlapping white card (path) → place (text + chips, globe) → news in a white panel → FAQ on canvas → gradient call card → full-bleed footer.
- Interior pages: white. Light page head (breadcrumb pill, title with the closing phrase in electric blue, lead, actions, the page photo with a light glass index of the sections) → the content: editorial pages as sections (text on the white, card grids on pale `--chiaro` panels, never two in a row; see *Interior sections*), lists as their own components (course wall, event rows, document filter and table, edition groups, news as the home's photo cards), articles and legal notes as text → on articles and course pages a pale panel with more news / other courses → the gradient call card on every interior page (the page's own closing call, else the home's).
- Bento (desktop): `cta s1 i1 s2 i2 / cta head head head head / wide wide list list s3`; 4 columns under 1100px; 2 under 640px.
- Course grid: 4 columns, 2 under 1100px, 1 under 640px. News: 3 cards, 1 under 900px.
- Header: sticky floating pill, detached from the top edge. On the home it is transparent over the footage and turns to dark glass after 24px of scroll; on every other page it is the same deep-navy pill from the start (white text, white CTA). Logged in, it sits below the WordPress toolbar. Under 1080px the nav collapses behind a "Menu" pill. A round search icon sits before the nav (beside the Menu pill on phones) and opens the site search in a white card under the bar, the cursor already in the field; it closes with Escape or a click elsewhere, like the language list. Under 390px the Menu pill keeps only its icon (the label stays for screen readers) and under 340px the logo narrows to 150px, so logo, search and menu always share one line.

## Elevation & Depth

Depth comes from layering: panels over the canvas, the partner bar over the hero's lower edge, glass over footage, the white path card over its photo. Motion is JavaScript-led and exists only while the footer switch "Animazioni" is on (remembered per visitor; flipping it reloads the page from the top; with no choice made, the system reduced-motion setting decides; hidden without JavaScript). The same motion runs on every page, interior ones included. On: the hero and page heads come in on load (headline words rise from a mask and sharpen from a blur, like a focus pull, then lead, actions, glass cards, partner bar; on course pages the large photo comes into focus); headings, cards, rows, tiles and footer columns rise in staggered batches as they enter the viewport; figures count up from zero; scrolling back up, whatever sinks into the bottom quarter of the screen goes out there, in view, the way it came in but quicker and fast from the first frame (cards fade and sink, headline words drop back into their mask, the footer logo un-draws, figures return to zero); when the scroll stops, what went out above the entry line comes back, so the screen is never left with gaps; every reveal plays again on the way down without a reload, while what leaves through the top stays in place; the hero footage, the path photo and the page-head photos drift with the scroll, and the path photo comes into focus as it appears; the globe turns and Rome pings; FAQ items open and close with a height animation; as soon as the footer comes into view the outlined footer logo is traced stroke by stroke from 0 to 100%, left to right, in about a second and a half; filtered course cards fade back in. Off: a static site, no transitions, video paused, accordions native. A reload always starts at the top of the page; back/forward return to where the visitor was. Nothing is hidden without JavaScript, and assistive tech reads the plain heading text and final figures. Shadows are soft and offset (`0 30px 60px -36px rgb(4 21 43 / 0.45)`); primary buttons carry a blue glow (`0 14px 32px -12px rgb(16 50 207 / 0.65)`). Glass: white text sits on navy glass (`rgb(4 21 43 / 0.42–0.5)`, blur 12–18px), dark text on light glass (white at 86%): measured on the real pixels behind the text, both keep WCAG AA over the brightest frame of a photo or of the footage. On phones the hero scrim covers the whole width, because the text does. Page-load headlines (hero, page heads, articles) arrive already split into words from the server (`its_split_words()`), and their rise starts from CSS with the first paint: they are the largest element on screen, and they must not wait for the script.

## Shapes

- 40px: panels, call card, page-head photo, article photo.
- 30px: tiles, cards, rows container, document table, filter bar.
- 22px: glass cards, news photo, index links.
- Pills: buttons, nav, chips, inputs, filters, breadcrumbs. Circles: arrows, icons, pause control, language.

## Components

### Buttons
Pills, 3rem, Raleway 600. **White** on dark/footage; **Gradient** (electric → blue, white text, blue glow) on light; **Glass** on footage; **Outline** on light; **Outline light** on dark. Arrow icons rotate 45° on hover; buttons lift 1px.

### Navigation
Logo, a glass pill holding the links (current page as a white inner pill), the language switcher, white CTA pill; the bar is navy on every page.

### Language switcher
A pill with a round flag (inline SVG: Italy, United Kingdom) and the language code in the header, or the language name in the footer next to the social icons (as in the hosting reference). It opens a white rounded list of the site's languages, each with its flag, name in its own language and a check on the current one; in the footer the list opens upwards. Native `<details>`; one open at a time, closed by a click elsewhere or Escape. Each link leads to the same page in the other language (Polylang), or to that language's home page. Flags are the only non-institutional colours on the site, and only at flag size.

### Glass cards
Over the hero footage: a stat card (87%) and a visit/next-event card with a white icon circle. On a course photo: a facts card (years, hours, internship, places) overlapping the photo's left edge.

### Bento tiles
Navy tiles with a hairline border: CTA tile with two pill actions, square stat tiles, square photo tiles, a wide photo with a glass chip, a list tile, and one gradient accent tile.

### Course card
Portrait photo card (min-height 25rem), bottom scrim, title and one line in white, glass area chip top-left, white circular arrow bottom-right that turns cyan and rotates on hover.

### Overlap block
Photo (8/12 columns, 40px) with a white card overlapping its right side, holding the three numbered steps and a gradient action.

### Comparison card
One navy card (max 860px, 40px, hairline cyan border, soft glow behind the section) headed by the logo: rows of label (with a cyan line icon) and value (with a gradient check circle), only published facts (2 years, ~1,000 hours, 800+ hours internship, public funding, 28 places, access, Erasmus+, 87%). Two pill actions centred below. One column under 640px.

### Place block
Text (display heading, lead) with four navy chips in a 2×2 grid (address, Metro B, hours, Erasmus+ link), each with an icon circle; beside it a dotted sphere (dots on a shaded navy disc, two hairline meridians, clipped) with a white "Roma" pin. Stacks under 1100px with the globe first.

### FAQ
Native `<details>` items in navy rounded rows (22px), question as h3, chevron that turns on open; the first item open. Content comes from the FAQ page (Details blocks or heading + paragraph). Works without JavaScript.

### Footer
Full-bleed navy band (no inset or corners, user decision, hairline top edge): logo, claim, circular social icons and the language switcher; three columns (offer, contacts, transparency + legal); the funding logos in a white rounded bar, as official vector files at their own colours (the national ITS logo «Istituti Tecnici Superiori – It's my future»; the ESF+ Lazio institutional block in brand-book order, equally spaced with grey rules: Coesione Italia 21-27 Lazio, EU emblem «Cofinanziato dall'Unione europea», emblem of the Italian Republic, Regione Lazio; then the PNRR Istruzione «Futura» band; always on one row from 761px, the marks and their spacing scaling together with the viewport; two marks per row on phones; sources in `assets/img/funding/SOURCES.md`); base row with ©, the animations switch, languages and a "Torna su" pill with a white arrow circle (smooth scroll while motion is on); the logo as a giant outlined watermark fading into the bottom edge.

### Interior page head
The light page head with the soft cyan glow; the title takes the display emphasis automatically (second sentence, else what follows the last comma, else the last two words) in electric blue 600; actions from the page's closing call (gradient first, outline after; a meeting when there is only one); over the photo's lower left edge a light glass card "On this page" with the numbered sections (under the photo below 900px). Parent pages appear in the breadcrumb by their short menu label.

### Interior sections
Read from core blocks with the theme's block styles: Paragraph *Occhiello* before a Heading 2 is the section chip (glass on dark, pale on panels), before a Heading 3 the card label; Heading 2 opens a section (anchor → hero index); Heading 3 opens a card (a link inside it makes the whole card clickable); List *Numeri* gives figure tiles (number in bold, then its label; the last on the gradient, counting up); Group *Invito finale* is the closing call. Labels are pale chips (white on the pale panels). A section's text goes beside its title (split, 1:1); figure tiles are pale with the last on the gradient; cards whose titles or labels are numbered (`01 ·`, `1 ·`) become steps with the home's round gradient numbers joined by a dashed rule; cards that all link to courses become course cards; other cards are rounded tiles with an uppercase label, title, text and a circular icon for where they lead (arrow, external, download, mail). Bulleted lists become rows with the gradient check.

### Text pages
Articles, events, documents, legal notes and the FAQ share one reading layout across the whole row: the text in a 48rem column (1.125rem, line-height 1.75, the first paragraph larger), a side card on the right that stays in view (pale `--chiaro`, 30px): facts (date, category, reading time; event time, mode, place; document type, cycle, format), an index of the headings, and the actions. On articles the photo heads the text column. Headings stand apart from the text: H2 in dark blue 600 with a short gradient rule above, H3 in institutional blue 600; lists use a blue dash, with "Label:" in bold. News imported without structure gets it at display time (a short line standing alone before more text becomes a heading, dashed lines become a list); stored content is not changed. Page heads without a photo split the row: title on the left, lead and actions on the right. Under 900px everything stacks (facts first on events and documents).

### Admin menu icons
Four 20×20 filled glyphs for the content types, built from the logo's shapes so they belong to the brand and still read at menu size: Corsi a clapperboard with the play triangle cut into the slate; Eventi a ticket with a perforation and the logo's circle; Edizioni dei corsi stacked cards, the front one marked with the logo's two bars (edition II); Documenti e bandi a sheet with a folded corner, lines and a round stamp. Filled, not stroked, so WordPress recolours them with the admin colour scheme like its Dashicons.

### Lists and documents
Rows in a rounded container with circular arrows; the document filter is a light rounded bar with pill fields; the table is a rounded bordered container.

## Do's and Don'ts

### Do:
- **Do** keep content in rounded containers and inset panels.
- **Do** set headlines light, in sentence case, with one emphasised phrase.
- **Do** use glass only over imagery.
- **Do** keep every action a pill with a verb, and the ↗ arrow on primary actions.
- **Do** use real photography; photos under 1200px stay in tiles and cards, never full width.

### Don't:
- **Don't** add yellow, green, red or per-area colours.
- **Don't** put a badge or label above a headline.
- **Don't** bring back mono micro-labels, counters, grid backgrounds or HUD frames.
- **Don't** put small white text on the cyan end of a gradient.
- **Don't** invent figures, testimonials or partners; published figures live in the Customizer.
- **Don't** bring in pricing-site devices: plan tiers, "most popular" badges, competitor columns.
