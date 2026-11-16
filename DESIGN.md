---
name: ITS Academy Roberto Rossellini
description: The current Rossellini site, made cinematic — dark fields, institutional blues and cyan, Raleway, the footage as the opening shot.
colors:
  blu: "#004590"
  blu-notte: "#002c64"
  ciano: "#49b7e9"
  elettrico: "#1032cf"
  bianco: "#ffffff"
  chiaro: "#f3f5f8"
  inchiostro: "#141414"
  nero: "#080b10"
  muted: "#4a5466"
  muted-on-dark: "#b9c6da"
  bordo: "#dfe4ec"
typography:
  hero:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(3.4rem, 10.6vw, 10.75rem)"
    fontWeight: 900
    lineHeight: 0.86
    letterSpacing: "-0.04em"
  headline:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(2.4rem, 5.2vw, 4.5rem)"
    fontWeight: 900
    lineHeight: 0.95
    letterSpacing: "-0.035em"
  headline-xl:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(2.75rem, 7.5vw, 7rem)"
    fontWeight: 900
    lineHeight: 0.9
    letterSpacing: "-0.04em"
  index-title:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(1.9rem, 4.4vw, 4rem)"
    fontWeight: 900
    lineHeight: 0.95
    letterSpacing: "-0.04em"
  headline-m:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(2rem, 3.6vw, 3rem)"
    fontWeight: 900
    lineHeight: 0.95
    letterSpacing: "-0.035em"
  page-title:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(2.6rem, 6vw, 5.25rem)"
    fontWeight: 900
    lineHeight: 0.95
    letterSpacing: "-0.035em"
  statement:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(1.6rem, 3vw, 2.5rem)"
    fontWeight: 800
    lineHeight: 1.08
    letterSpacing: "-0.03em"
  card-title:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(1.2rem, 1.5vw, 1.4rem)"
    fontWeight: 800
    lineHeight: 1.15
    letterSpacing: "-0.02em"
  row-title:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(1.15rem, 1.8vw, 1.5rem)"
    fontWeight: 700
    lineHeight: 1.22
    letterSpacing: "-0.015em"
  body:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "1.0625rem"
    fontWeight: 400
    lineHeight: 1.6
  lead:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "clamp(1.125rem, 1.6vw, 1.375rem)"
    fontWeight: 400
    lineHeight: 1.45
  ui:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "0.9375rem"
    fontWeight: 700
    lineHeight: 1.1
  small:
    fontFamily: "Raleway, Segoe UI, system-ui, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 700
rounded:
  s: "10px"
  m: "14px"
  xl: "28px"
  pill: "999px"
spacing:
  gutter: "clamp(1rem, 4vw, 3.5rem)"
  section: "clamp(4.5rem, 9vw, 8rem)"
  header: "5rem"
  wrap: "1360px"
components:
  button-cyan:
    backgroundColor: "{colors.ciano}"
    textColor: "{colors.blu-notte}"
    typography: "{typography.ui}"
    rounded: "{rounded.pill}"
    padding: "0.75rem 1.5rem"
    height: "3rem"
  button-cyan-hover:
    backgroundColor: "{colors.bianco}"
  button-blue:
    backgroundColor: "{colors.blu}"
    textColor: "{colors.bianco}"
    typography: "{typography.ui}"
    rounded: "{rounded.pill}"
    padding: "0.75rem 1.5rem"
    height: "3rem"
  button-blue-hover:
    backgroundColor: "{colors.elettrico}"
  card:
    backgroundColor: "{colors.bianco}"
    textColor: "{colors.inchiostro}"
    rounded: "{rounded.m}"
  filter-pill:
    backgroundColor: "{colors.bianco}"
    textColor: "{colors.blu-notte}"
    typography: "{typography.ui}"
    rounded: "{rounded.pill}"
    height: "2.75rem"
  filter-pill-active:
    backgroundColor: "{colors.blu-notte}"
    textColor: "{colors.bianco}"
  input:
    backgroundColor: "{colors.bianco}"
    textColor: "{colors.inchiostro}"
    rounded: "{rounded.s}"
    height: "3rem"
    padding: "0 1rem"
---

# Design System: ITS Academy Roberto Rossellini

## Overview

**Creative North Star: "Lo stesso Rossellini, al cinema"**

The system keeps what makes the current itsrossellini.it recognisable and removes what makes it read as machine-made. Kept: the institutional blues with cyan, Raleway set heavy and uppercase with a second line in italic, the real hero video, real photography, rounded course cards, pill buttons, the news list with large titles, partner logos in tiles. Removed: monospace micro-labels, section counters, kickers, grid-line backgrounds, glows, outlined giant numbers, the "REC" camera frame, coloured stripes, the marquee, 9–10px text.

Modern here means a cinematic home: dark fields, the real footage as the opening shot, type at poster scale, and two authored moves only. The footage closes into a rounded frame as the page starts to scroll, and the course index shows each course's photo at the pointer. Reading pages (courses, news, documents) stay light for legibility.

**Key Characteristics:**
- Institutional palette only: blue, deep blue, cyan, electric blue (hover and focus), near-black, white, a cool light grey.
- Raleway everywhere; headlines in 900 caps with an italic 500 second line.
- Real footage and photographs in 14px rounded frames.
- Pill buttons in sentence case; circular arrow buttons on list rows.
- Home fields: footage → near-black (courses) → deep blue (academy) → near-black (outcome, path) → deep blue (news) → near-black (partners) → cyan (call) → near-black footer, each sliding over the previous with 28px rounded shoulders.

## Colors

### Primary
- **Blu notte** (`#002c64`): dark sections, masthead, page heads, filled active states, primary buttons on cyan.
- **Blu** (`#004590`): links, secondary headline lines on light grounds, blue buttons, list bullets, date cells.

### Secondary
- **Ciano** (`#49b7e9`): the single accent. Primary buttons on dark grounds, the italic headline line on dark grounds, the final call band, list dots, focus rings on dark. Never text on white (2.3:1); on `#004590` only at large sizes.
- **Elettrico** (`#1032cf`): hover for links and blue buttons, focus ring on light grounds. Never a surface.

### Neutral
- **Bianco** (`#ffffff`) and **Chiaro** (`#f3f5f8`, cool light grey) alternate as light grounds. The current site's warm paper `#f5f4ef` is replaced by the cool grey to sit with the blues.
- **Inchiostro** (`#141414`): headlines and body on light grounds.
- **Nero** (`#080b10`): footer only.
- **Muted** (`#4a5466`, 7.6:1 on white) and **Muted on dark** (`#b9c6da`, 7.9:1 on deep blue) for secondary text.
- **Bordo** (`#dfe4ec`): hairlines, card and tile borders. On dark grounds hairlines are white at 20%.

### Named Rules
**The Institute Colours Only Rule.** No hue outside the palette: no yellow, green, red or category colours. Technological areas are told apart by name, not by colour.

**The One Accent Rule.** Cyan is the only accent. It is a fill or large text, never small text on light grounds.

## Typography

**Font:** Raleway (variable 100–900, normal and italic, self-hosted, OFL), with `font-variant-numeric: lining-nums` so figures sit on the baseline.

### Hierarchy
- **Hero** (900, clamp 3.4–8.25rem, 0.88, −0.04em, caps): the home headline only; the italic phrase in cyan stays on one line.
- **Headline** (900, clamp 2.4–4.5rem, 0.95, −0.035em, caps): section headings; the `<em>` second line is italic 500 on its own line, blue on light grounds, cyan on dark, deep blue on cyan.
- **Headline M** (900, clamp 2–3rem): secondary section headings.
- **Page title** (900, clamp 2.6–5.25rem, caps): interior h1; titles longer than 26 characters switch to 800-ish sentence case at clamp 2.1–3.6rem.
- **Statement** (800, clamp 1.6–2.5rem, −0.03em): the big sentence in the path steps; the 87% claim uses 700 with the figure in 900 cyan.
- **Card title** (800, clamp 1.2–1.4rem) and **Row title** (700, clamp 1.15–1.5rem).
- **Body** (400, 1.0625rem, 1.6), measure 50ch; **Lead** (400, clamp 1.125–1.375rem, 1.45).
- **UI** (700, 0.9375rem): buttons, filters, nav (600), meta lines.
- **Small** (700, 0.875rem): area labels, table headers, badges (0.8125rem).

### Named Rules
**The Two-Voice Headline Rule.** A section headline is heavy caps, optionally followed by one italic line in the accent colour. Never a third voice, never a label above it.

**The Tracking Floor Rule.** Letter-spacing never tighter than −0.04em, whatever the current site does.

## Layout

- Container `min(100% - 2 × gutter, 1360px)`, gutter `clamp(1rem, 4vw, 3.5rem)`, section rhythm `clamp(4.5rem, 9vw, 8rem)`.
- Home sequence: hero footage (165svh tall with a sticky 100svh stage when scroll-driven animation is supported, 100svh otherwise) → course index on near-black → academy showcase on deep blue → outcome and path on near-black → news rows on deep blue → partners on near-black → call on cyan → footer on near-black with the logo across the full width. Every section after the courses carries `section--stack`: 28px rounded top corners and a −28px overlap.
- Course index (home): one row per course, title | area + line | arrow; under 760px it becomes a horizontal snapping carousel of 4:5 photo cards. Course grid (interior pages): 4 columns, 2 under 1100px, 1 under 640px.
- Sticky masthead, 5rem tall; on the home page it is transparent over the video and turns deep blue after 24px of scroll.
- Breakpoints: 1100px, 1080px (navigation behind the Menu button), 900px, 760px (document table to rows), 640px.

## Elevation & Depth

Mostly flat. Depth comes from the stack: each dark section slides over the previous one with rounded shoulders. One shadow exists: the course card hover lift `0 18px 40px -22px rgb(0 44 100 / 0.45)` on interior grids. The hero and the course cover heads use a deep-blue legibility scrim over the footage; no other gradients, no glows, no glass.

## Shapes

- Full-width bands (the closing hero frame, the academy photo, section shoulders, footer top): 28px.
- Photographs, cards, course previews, facts panels, filter bar: 14px radius.
- Inputs and partner tiles: 10px.
- Buttons, filter pills, badges, search field: full pill.
- Arrow buttons on list rows and the video control: circles.
- Rules: 1px hairlines; 1.5px ink rule to open a list.

## Components

### Buttons
Pills, 3rem tall, Raleway 700 at 0.9375rem, sentence case. **Cyan** (deep-blue text) is the primary action on dark grounds and the video; hover goes white. **Blue** on light grounds; hover electric. **Navy** on the cyan band. **Ghost** (white outline) and **Outline** (deep-blue outline) for secondary actions; hover fills. Labels name the action.

### Chips and badges
Filter pills: white with a hairline border, deep-blue fill when active. Status badges on events: cyan "In programma", blue "Oggi", grey "Concluso". The course area on the page head is a cyan-outlined pill with white text.

### Cards
Course card: white, 1px border, 14px radius, 4:3 photo, title, area in blue below the title, one-line teaser, "Scopri il corso" link with arrow. Hover: lift, soft shadow, photo zoom 1.04, title turns blue. The whole card is one link.

### Rows
News and event rows: date cell (day 900 in blue, month and year), title 700, meta line, circular arrow on the right; the whole row is the link, hover tints the row and fills the arrow blue.

### Inputs / Fields
White, 1.5px light border, 10px radius, 3rem tall, blue caret; labels above in 700. The document filter sits in a deep-blue rounded bar with a cyan "Filtra" button.

### Navigation
Logo left; links at 600 with a 2px cyan underline for hover and current page; cyan "Preiscriviti" pill. Under 1080px a pill "Menu" button opens a deep-blue panel with large 800 links; Escape closes it. Without JavaScript the links stay visible.

### Course index (home)
Large Raleway 900 caps titles in white on near-black; area in cyan and one line in muted text; circular arrow. Hovering or focusing a row turns its title cyan, dims the others, rotates the arrow into a cyan fill and shows the course photo: beside the row without JS, following the pointer (with a slight lag and tilt) on fine pointers. Phones get a snapping carousel of photo cards instead.

### Course cover head
On a course page whose photo is at least 1200px wide, the photo fills the head behind the title under a deep-blue scrim; smaller photos sit beside the title.

### Hero video
Muted, looping, `playsinline`, no autoplay attribute: the script starts it only when reduced motion and data saving are off. A circular pause/play button is always available; the front page's featured image is the poster. Where `animation-timeline: scroll()` is supported and motion is allowed, the footage closes from full bleed into `inset(7svh gutter 9svh round 28px)` over the first 65svh of scroll while the copy drifts up 6svh; otherwise the hero is a static full-viewport stage.

## Do's and Don'ts

### Do:
- **Do** keep the current site's voice: heavy caps headline with an italic second line.
- **Do** use only the institutional palette, with cyan as the single accent.
- **Do** show real footage and real photographs, rounded at 14px.
- **Do** keep every action a pill with a verb.
- **Do** keep content visible without JavaScript and without scrolling animations.
- **Do** keep the two authored moves (closing frame, pointer preview) and add no third.

### Don't:
- **Don't** add yellow, green, red or per-area colours.
- **Don't** bring back monospace micro-labels, "01 / 05" counters, kickers, grid backgrounds, glows, outlined numbers, camera frames, coloured card stripes or the marquee.
- **Don't** set text below 13px.
- **Don't** put cyan text on white.
- **Don't** invent figures, testimonials or partners; published figures live in the Customizer.
