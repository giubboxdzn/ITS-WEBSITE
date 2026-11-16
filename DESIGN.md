---
name: ITS Academy Roberto Rossellini
description: Cinema print paper (locandina and fotobuste) in two inks for a two-year audiovisual academy in Rome.
colors:
  blu: "#004590"
  blu-notte: "#002c64"
  giallo: "#ffc61a"
  carta: "#ffffff"
  ink-rosso: "#c0392b"
  ink-verde: "#1d6b45"
  ink-nero: "#1c1d21"
  muted: "#3d5578"
  muted-on-blue: "#c9d8ee"
  rule: "#c5cfdd"
  hover-paper: "#f1f5fa"
typography:
  poster-title:
    fontFamily: "Sofia Sans Extra Condensed, Arial Narrow, sans-serif"
    fontSize: "clamp(4.25rem, 11.5vw, 10.5rem)"
    fontWeight: 900
    lineHeight: 0.82
    letterSpacing: "-0.01em"
  display:
    fontFamily: "Sofia Sans Extra Condensed, Arial Narrow, sans-serif"
    fontSize: "clamp(2.75rem, 6vw, 5.25rem)"
    fontWeight: 850
    lineHeight: 0.9
    letterSpacing: "-0.005em"
  page-title:
    fontFamily: "Sofia Sans Extra Condensed, Arial Narrow, sans-serif"
    fontSize: "clamp(3rem, 7vw, 6rem)"
    fontWeight: 900
    lineHeight: 0.88
  card-title:
    fontFamily: "Sofia Sans Extra Condensed, Arial Narrow, sans-serif"
    fontSize: "clamp(1.75rem, 2.3vw, 2.25rem)"
    fontWeight: 850
    lineHeight: 0.92
  billing:
    fontFamily: "Sofia Sans Extra Condensed, Arial Narrow, sans-serif"
    fontSize: "clamp(1.15rem, 1.8vw, 1.6rem)"
    fontWeight: 650
    letterSpacing: "0.02em"
  body:
    fontFamily: "Sofia Sans, Segoe UI, system-ui, sans-serif"
    fontSize: "1.0625rem"
    fontWeight: 400
    lineHeight: 1.55
  lead:
    fontFamily: "Sofia Sans, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(1.125rem, 1.6vw, 1.375rem)"
    fontWeight: 400
    lineHeight: 1.45
  ui:
    fontFamily: "Sofia Sans, Segoe UI, system-ui, sans-serif"
    fontSize: "1rem"
    fontWeight: 750
    lineHeight: 1.1
  small:
    fontFamily: "Sofia Sans, Segoe UI, system-ui, sans-serif"
    fontSize: "0.9375rem"
    fontWeight: 650
  caption:
    fontFamily: "Sofia Sans, Segoe UI, system-ui, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 650
  label:
    fontFamily: "Sofia Sans, Segoe UI, system-ui, sans-serif"
    fontSize: "0.8125rem"
    fontWeight: 750
    letterSpacing: "0.06em"
rounded:
  none: "0px"
spacing:
  gutter: "clamp(1rem, 4vw, 3.5rem)"
  section: "clamp(4rem, 8vw, 7rem)"
  print-margin: "clamp(0.4rem, 0.8vw, 0.6rem)"
  wrap: "1360px"
components:
  button-yellow:
    backgroundColor: "{colors.giallo}"
    textColor: "{colors.blu-notte}"
    rounded: "{rounded.none}"
    padding: "0.8rem 1.35rem"
    height: "3rem"
  button-yellow-hover:
    backgroundColor: "{colors.carta}"
    textColor: "{colors.blu-notte}"
  button-blue:
    backgroundColor: "{colors.blu}"
    textColor: "{colors.carta}"
    rounded: "{rounded.none}"
    padding: "0.8rem 1.35rem"
    height: "3rem"
  button-blue-hover:
    backgroundColor: "{colors.blu-notte}"
  area-chip:
    backgroundColor: "{colors.ink-rosso}"
    textColor: "{colors.carta}"
    typography: "{typography.label}"
    rounded: "{rounded.none}"
    padding: "0.35rem 0.55rem"
  input:
    backgroundColor: "{colors.carta}"
    textColor: "{colors.blu-notte}"
    rounded: "{rounded.none}"
    height: "3rem"
    padding: "0 0.9rem"
---

# Design System: ITS Academy Roberto Rossellini

## Overview

**Creative North Star: "La carta del cinema"**

The site is printed matter from an Italian cinema lobby, not a screen pretending to be film equipment. The front page is a locandina: one huge condensed title, one photograph in a white margin, a billing block of facts at the foot. Each course is a fotobusta, the horizontal lobby card: photo printed in a single ink, white border, title band below. Dated things carry a programme stamp, the way a cinema marks what is coming, on today, or gone.

Two inks do almost all the work: the institutional blue (binding brand asset) as the dominant field, chrome yellow as the second ink for titles, calls to action and highlights. White poster stock is the paper. A third ink only ever appears inside photographs, and it is fixed per technological area, so the colour of a photo tells you the area before you read it.

Restraint lives in the materials, not in the energy: no shadows, no gradients, no glows, no rounded corners, no HUD labels. Commitment lives in scale: titles are set big and tight, photographs are large, sections alternate whole fields of blue, white and yellow.

**Key Characteristics:**
- Two inks on white: blue field, yellow second ink, white paper.
- Photographs in bicromia, one fixed ink per technological area.
- Extra-condensed gothic in caps for every title; a regular-width sibling for reading.
- Facts set as a poster billing block, never as stat tiles.
- Printed rules (1–2px lines) instead of boxes, shadows or cards-in-cards.
- Programme stamps computed from real dates.

## Colors

The palette is a two-ink print run plus area inks reserved for photography.

### Primary
- **Blu istituzionale** (`#004590`): the binding brand blue. Dominant field of the masthead, the locandina, interior page heads, the claim section and the events board. Also the default photo ink (Cinema & TV).
- **Blu notte** (`#002c64`): body text on white, footer field, the course billing band, filled filter state.

### Secondary
- **Giallo cromo** (`#ffc61a`): the second ink. Poster titles on blue, primary buttons, emphasis inside claims, the yellow section fields, focus rings on blue grounds, text selection. Never used as text on white (contrast 1.6:1).

### Tertiary
- **Area inks**, used only for duotone photographs, the area chip printed on the photo and the swatch in the area filter: Cinema & TV `#004590`, Multimedia `#c0392b`, Audio `#1d6b45`, Tech `#1c1d21`. Each is a fixed mapping (filter `its_rossellini_area_inks`), never decoration.

### Neutral
- **Carta** (`#ffffff`): the poster stock. Pure white, never cream.
- **Muted** (`#3d5578`): secondary text on white (7.6:1).
- **Muted on blue** (`#c9d8ee`): secondary text on blue fields (6.4:1 on blu, 9.4:1 on blu notte).
- **Rule** (`#c5cfdd`): printed hairlines on white. On blue grounds rules are white at 32% opacity.

### Named Rules
**The Two Inks Rule.** Page chrome uses only blue, yellow and white. A third colour on the page must come from a photograph's area ink.

**The Yellow Is Not Text On Paper Rule.** Yellow is a field or text on blue, never text on white.

**The Ink Means Area Rule.** An area ink appears only where it identifies an area (photo duotone, area chip, filter swatch). If it does not say "this is Audio", it is not used.

## Typography

**Display Font:** Sofia Sans Extra Condensed (variable, self-hosted, OFL), fallback Arial Narrow.
**Body Font:** Sofia Sans (variable, self-hosted, OFL), fallback Segoe UI / system-ui.

One superfamily set like a poster: titles in the extra-condensed cut, uppercase, heavy (850–900), tight leading (0.82–0.95); reading text in the regular width at 17px with 1.55 leading. No monospace anywhere.

### Hierarchy
- **Poster title** (900, clamp 4.25–10.5rem, 0.82): the front page title only. Printed in two plates (white under yellow) that settle into register on load.
- **Page title** (900, clamp 3–6rem, 0.88): interior h1 on the blue head; titles over 26 characters drop to clamp 2.6–4.5rem.
- **Display** (850, clamp 2.75–5.25rem, 0.9): section headings (h2).
- **Card title** (850, clamp 1.75–2.25rem, 0.92): fotobusta titles; feature cards up to 3rem.
- **Billing** (650, clamp 1.15–1.6rem, uppercase, +0.02em): fact lines, with the figure in yellow at 900.
- **Body** (400, 1.0625rem, 1.55): reading text, measure 53ch (about 75 characters).
- **Lead** (400, clamp 1.125–1.375rem, 1.45): one-sentence introductions under titles.
- **UI** (750, 1rem, 1.1): button labels.
- **Small** (650, 0.9375rem): filter tabs, meta lines, card links, breadcrumbs, footer base.
- **Caption** (650, 0.875rem): field and table headers, fact labels.
- **Label** (750, 0.8125rem, uppercase, +0.06em): the area chip only.

### Named Rules
**The No Kicker Rule.** Nothing sits above a heading. Area chips and meta lines go after the title, or are printed onto the photo.

**The Caps Are For Titles Rule.** Uppercase is reserved for condensed titles, billing figures and the area chip. Running text and long credit lines stay in sentence case.

## Layout

- Container: `min(100% - 2 × gutter, 1360px)`, gutter `clamp(1rem, 4vw, 3.5rem)`.
- Sections alternate whole fields: blue → white → yellow → blue → white → yellow → blue notte footer. Vertical rhythm `clamp(4rem, 8vw, 7rem)`.
- The course wall is a 12-column lobby: the first two visible courses span 6 columns (16:10 photos), the rest span 4 (4:3). 6 columns under 1100px, one column under 640px.
- Interior pages: blue head with breadcrumb, title, lead and optional portrait photo (4:5) in a white print margin; body below in a 53ch measure, or split 17rem facts / content for events and documents.
- Breakpoints: 1100px (wall to two columns, footer to two columns), 1080px (navigation collapses behind the Menu button), 900px (all split grids stack), 760px (document table to stacked rows), 640px (single column).

## Elevation & Depth

Flat by rule. Depth comes from print: the white margin around photos, overlapping plates in the poster title, and the stacking of fields. There are no shadows in the system.

### Named Rules
**The Print Has No Shadow Rule.** No box-shadow, no glow, no blur, no glass. Hover changes ink (border to blue notte, duotone lifting to full colour), never elevation.

## Shapes

Square everything: radius 0 on buttons, inputs, chips, cards and photos. Lines are printed rules: 1px hairlines for separation, 1.5–2px blue notte rules to start a list or a group. The only rotated element is the programme stamp (−3°).

## Components

### Buttons
Rectangular, 3rem tall, 700–750 weight. **Yellow** is the primary action on blue (hover: white). **Blue** is the primary action on white (hover: blue notte). **Line** is the secondary action (2px current-colour border; hover fills white with blue notte text). `:active` nudges down 1px. Labels name the action: "Prenota un incontro", "Preiscriviti a questo corso".

### Chips
The **area chip** is printed onto the top-left corner of a fotobusta photo in the area ink, white label text. On the course page head it inverts: white chip, area-ink text.

### Cards / Containers
The **fotobusta** is the only card. White paper, 1px rule border, print margin around the photo, title band below (title, area, one-line teaser, "Scheda del corso" with arrow). Photos are duotone at rest (grayscale image with the area ink screen-blended on top) and lift to full colour on hover and focus. A course without a photo prints its area name as type on an ink field.

### Inputs / Fields
White field, 2px border, square, 3rem tall, blue notte text, blue caret. Labels sit above in 700 weight. On the blue filter bar the border is white; on paper it is blue notte. Placeholders show an example and end with "…".

### Navigation
Blue masthead, white logo, links at 650 weight with a 2px yellow underline for hover and current page; "Preiscriviti" as a small yellow button. Under 1080px a "Menu" button opens a full-width blue panel listing the links in condensed caps, closed with Escape. Without JavaScript the links stay visible and wrap.

### Billing Block
The poster's credits: one centred line of facts in condensed caps with each figure in yellow (2 anni · 1.000 ore in aula e laboratorio · 800+ ore di stage · 28 posti), then a sentence-case credit line with the address and the funding bodies. Course pages use the same line left-aligned beside the actions.

### Programme Stamp
A small outlined label in condensed caps, rotated −3°, computed from a real date: "Prossimamente" (red on paper, yellow on blue), "Oggi" / "Ultimi giorni" (yellow fill), "Concluso" / "Chiuso" (muted). Never shown for undated items.

### Date Slot
Every dated item puts its date in the same fixed cell on the left: day in 900 weight, month abbreviation, year. News and events share it.

## Do's and Don'ts

### Do:
- **Do** print photographs in the area's ink and keep the white margin around them.
- **Do** set every title in the extra-condensed cut, uppercase, tight.
- **Do** set facts as a billing line with the figure in yellow.
- **Do** compute stamps from real dates and hide them when there is no date.
- **Do** keep content visible without JavaScript; motion only settles what is already there.
- **Do** keep reading text at a 53ch measure.

### Don't:
- **Don't** put a kicker, eyebrow or section number above a heading.
- **Don't** use shadows, glows, gradients, glass or rounded corners.
- **Don't** bring back the HUD costume: timecodes, REC dots, crosshairs, coordinates, mono labels.
- **Don't** use yellow as text on white.
- **Don't** show facts as stat tiles (small label over a big number).
- **Don't** hide content behind scroll-triggered reveals.
- **Don't** invent figures, testimonials or partners; published figures live in the Customizer.
