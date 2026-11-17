#!/usr/bin/env python3
"""Fetch public content from itsrossellini.it into .wp/seed/ for LOCAL preview only.

Read-only: it only performs GET requests against the public REST API and
public pages. Nothing is written to the live site. The output stays in the
gitignored .wp/ folder and is never committed.

Usage: python3 -I dev/seed/fetch.py [--out .wp/seed]
"""
import argparse
import html
import json
import os
import re
import sys
import urllib.parse
import urllib.request
from html.parser import HTMLParser

BASE = "https://itsrossellini.it"
UA = "its-rossellini-theme-dev/1.0"


def get(url, binary=False):
    req = urllib.request.Request(url, headers={"User-Agent": UA})
    with urllib.request.urlopen(req, timeout=40) as r:
        data = r.read()
    return data if binary else data.decode("utf-8")


def rest(path, **params):
    params.setdefault("per_page", 100)
    out, page = [], 1
    while True:
        params["page"] = page
        url = f"{BASE}/wp-json/wp/v2/{path}?{urllib.parse.urlencode(params)}"
        try:
            batch = json.loads(get(url))
        except Exception:
            break
        if not isinstance(batch, list) or not batch:
            break
        out.extend(batch)
        if len(batch) < params["per_page"]:
            break
        page += 1
    return out


def is_it(item):
    return "/en/" not in item.get("link", "")


def main_html(page_html):
    m = re.search(r"<main.*?</main>", page_html, re.S)
    body = m.group(0) if m else page_html
    return re.sub(r"<(script|style|svg|noscript|template)[^>]*>.*?</\1>", "", body, flags=re.S)


def text_lines(fragment):
    body = re.sub(r"<(h[1-6]|p|li|a|button|span|div|section|article|figcaption|strong|dt|dd|td|th|time)[^>]*>", "\n", fragment)
    text = html.unescape(re.sub(r"<[^>]+>", "", body))
    out, prev = [], None
    for line in (x.strip() for x in text.split("\n")):
        if line and line != prev:
            out.append(line)
        prev = line
    return out


def to_blocks(fragment):
    """Convert a rendered <main> into plain semantic HTML (h2/h3/p/ul) for the editor."""
    frag = re.sub(r"<(nav|header|form|button)[^>]*>.*?</\1>", "", fragment, flags=re.S)
    tokens = re.findall(r"<(h[1-6]|p|li)[^>]*>(.*?)</\1>", frag, flags=re.S)
    seen, out, in_list = set(), [], False
    for tag, inner in tokens:
        inner = re.sub(r"<(?!/?(a|strong|em|br)\b)[^>]+>", "", inner).strip()
        inner = re.sub(r"\s+", " ", inner)
        plain = html.unescape(re.sub(r"<[^>]+>", "", inner)).strip()
        if not plain or plain in seen or len(plain) < 2 or re.fullmatch(r"[\d\s/·.]+", plain):
            continue
        seen.add(plain)
        if tag == "li":
            if not in_list:
                out.append("<ul>")
                in_list = True
            out.append(f"<li>{inner}</li>")
            continue
        if in_list:
            out.append("</ul>")
            in_list = False
        if tag == "h1":
            continue
        level = "h2" if tag in ("h2",) else ("h3" if tag.startswith("h") else "p")
        out.append(f"<{level}>{inner}</{level}>")
    if in_list:
        out.append("</ul>")
    return "\n".join(out)


# ---------- Editorial pages -> block editor content ----------
# The live interior pages share one template (hero, sections with an eyebrow, facts, card grids, a closing
# call). They become core blocks with the theme's block styles, the same an editor would use:
# Paragraph "Occhiello" (is-style-eyebrow), List "Numeri" (is-style-facts), Group "Invito finale" (is-style-callout).

VOID = {"area", "base", "br", "col", "embed", "hr", "img", "input", "link", "meta", "source", "track", "wbr"}


class Node:
    __slots__ = ("tag", "attrs", "children", "parent")

    def __init__(self, tag, attrs, parent):
        self.tag, self.attrs, self.children, self.parent = tag, dict(attrs), [], parent

    def has(self, name):
        return name in (self.attrs.get("class") or "").split()

    def elements(self):
        return [c for c in self.children if isinstance(c, Node)]

    def find_all(self, pred):
        out = []
        for c in self.elements():
            if pred(c):
                out.append(c)
            out.extend(c.find_all(pred))
        return out

    def find(self, pred):
        hits = self.find_all(pred)
        return hits[0] if hits else None

    def text(self):
        return re.sub(r"\s+", " ", "".join(c if isinstance(c, str) else c.text() for c in self.children)).strip()


class Tree(HTMLParser):
    def __init__(self, markup):
        super().__init__(convert_charrefs=True)
        self.root = self.cur = Node("root", [], None)
        self.feed(markup)
        self.close()

    def handle_starttag(self, tag, attrs):
        node = Node(tag, attrs, self.cur)
        self.cur.children.append(node)
        if tag not in VOID:
            self.cur = node

    def handle_startendtag(self, tag, attrs):
        self.cur.children.append(Node(tag, attrs, self.cur))

    def handle_endtag(self, tag):
        node = self.cur
        while node is not None and node.tag != tag:
            node = node.parent
        if node is not None and node.parent is not None:
            self.cur = node.parent

    def handle_data(self, data):
        self.cur.children.append(data)


def local_url(href):
    """Links between pages of the site become root-relative (they work on the local copy); files stay on the live site."""
    return re.sub(r"^https?://(?:www\.)?itsrossellini\.it(?=/)(?!/wp-content/)", "", href.strip())


def inline(node, in_link=False):
    out = []
    for c in node.children:
        if isinstance(c, str):
            out.append(html.escape(c, quote=False))
        elif c.attrs.get("aria-hidden") == "true" or (in_link and c.tag == "small"):
            # Decorative arrows, and the document-type tag the live links repeat before the title.
            continue
        elif c.tag in ("strong", "b"):
            out.append(f"<strong>{inline(c, in_link)}</strong>")
        elif c.tag in ("em", "i"):
            out.append(f"<em>{inline(c, in_link)}</em>")
        elif c.tag == "a" and c.attrs.get("href"):
            out.append(f'<a href="{html.escape(local_url(c.attrs["href"]))}">{inline(c, True)}</a>')
        elif c.tag == "br":
            out.append("<br>")
        else:
            out.append(inline(c, in_link))
    return re.sub(r"\s+", " ", "".join(out)).strip()


def b_para(inner, style=""):
    attrs = f' {{"className":"{style}"}}' if style else ""
    cls = f' class="{style}"' if style else ""
    return f"<!-- wp:paragraph{attrs} -->\n<p{cls}>{inner}</p>\n<!-- /wp:paragraph -->"


def b_heading(level, inner, anchor=""):
    attrs = f' {{"level":{level}}}' if level != 2 else ""
    ident = f' id="{html.escape(anchor)}"' if anchor else ""
    return f'<!-- wp:heading{attrs} -->\n<h{level} class="wp-block-heading"{ident}>{inner}</h{level}>\n<!-- /wp:heading -->'


def b_list(items, style="", ordered=False):
    attrs = ("{" + ",".join(x for x in ('"ordered":true' if ordered else "", f'"className":"{style}"' if style else "") if x) + "}")
    attrs = f" {attrs}" if attrs != "{}" else ""
    tag = "ol" if ordered else "ul"
    lis = "".join(f"<!-- wp:list-item -->\n<li>{i}</li>\n<!-- /wp:list-item -->" for i in items)
    return f'<!-- wp:list{attrs} -->\n<{tag} class="wp-block-list{" " + style if style else ""}">{lis}</{tag}>\n<!-- /wp:list -->'


def b_buttons(links):
    inner = "".join(
        f'<!-- wp:button -->\n<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="{html.escape(h)}">{t}</a></div>\n<!-- /wp:button -->'
        for h, t in links
    )
    return f'<!-- wp:buttons -->\n<div class="wp-block-buttons">{inner}</div>\n<!-- /wp:buttons -->'


def b_group(blocks, style):
    return (f'<!-- wp:group {{"className":"{style}","layout":{{"type":"constrained"}}}} -->\n'
            f'<div class="wp-block-group {style}">' + "\n".join(blocks) + "</div>\n<!-- /wp:group -->")


def editorial_components(node, out):
    for el in node.elements():
        if el.has("inner-section-heading") or el.has("inner-section-menu") or el.tag in ("nav", "script", "form"):
            continue
        if el.tag == "dl":
            items = []
            for row in el.elements():
                dt, dd = row.find(lambda n: n.tag == "dt"), row.find(lambda n: n.tag == "dd")
                if dt and dd:
                    items.append(f"<strong>{inline(dd)}</strong> {inline(dt)}")
            if items:
                out.append(b_list(items, "is-style-facts"))
        elif el.tag in ("ul", "ol"):
            items = [inline(li) for li in el.elements() if li.tag == "li" and li.text()]
            if items:
                out.append(b_list(items, ordered=el.tag == "ol"))
        elif el.has("inner-card"):
            meta = el.find(lambda n: n.has("inner-card-meta"))
            title = el.find(lambda n: n.tag in ("h3", "h4"))
            more = el.find(lambda n: n.has("inner-card-link"))
            if meta:
                out.append(b_para(inline(meta), "is-style-eyebrow"))
            if title:
                inner = inline(title)
                if "<a " not in inner and more and more.attrs.get("href"):
                    inner = f'<a href="{html.escape(local_url(more.attrs["href"]))}">{inner}</a>'
                out.append(b_heading(3, inner))
            for p in el.find_all(lambda n: n.tag == "p" and not n.has("inner-card-meta")):
                if p.text():
                    out.append(b_para(inline(p)))
        elif el.tag == "p":
            if el.text():
                out.append(b_para(inline(el), "is-style-eyebrow" if el.has("inner-eyebrow") else ""))
        elif el.tag in ("h3", "h4"):
            out.append(b_heading(3, inline(el)))
        else:
            editorial_components(el, out)


def editorial(page_html, media_dir):
    """Content, excerpt (the hero lead) and featured image of a live interior page; None for other templates."""
    if "sub-editorial-page" not in page_html:
        return None
    root = Tree(main_html(page_html)).root
    page = root.find(lambda n: n.has("sub-editorial-page")) or root
    blocks = []
    for sec in page.find_all(lambda n: n.has("inner-content-section") or n.has("inner-related") or n.has("inner-callout")):
        if sec.has("inner-callout"):
            inner = []
            for el in sec.find_all(lambda n: n.tag in ("h2", "p")):
                inner.append(b_heading(2, inline(el)) if el.tag == "h2" else b_para(inline(el)))
            links = [(local_url(a.attrs["href"]), inline(a)) for a in sec.find_all(lambda n: n.tag == "a" and n.has("inner-button") and n.attrs.get("href"))]
            if links:
                inner.append(b_buttons(links))
            blocks.append(b_group(inner, "is-style-callout"))
            continue
        head = sec.find(lambda n: n.has("inner-section-heading"))
        eyebrow = head and head.find(lambda n: n.has("inner-eyebrow"))
        h2 = head and head.find(lambda n: n.tag == "h2")
        if eyebrow and eyebrow.text():
            blocks.append(b_para(inline(eyebrow), "is-style-eyebrow"))
        if h2:
            blocks.append(b_heading(2, inline(h2), sec.attrs.get("id", "")))
        editorial_components(sec, blocks)
    lead = page.find(lambda n: n.has("inner-lead"))
    img = page.find(lambda n: n.has("inner-hero-image"))
    src = ""
    if img:
        candidates = [c.strip().rsplit(" ", 1) for c in (img.attrs.get("srcset") or "").split(",") if c.strip()]
        sized = sorted(((int(w[:-1]), u) for u, w in candidates if w.endswith("w") and w[:-1].isdigit()), reverse=True)
        src = sized[0][1] if sized else img.attrs.get("src", "")
    return {
        "content": "\n\n".join(blocks),
        "excerpt": lead.text() if lead else "",
        "image": download(src, media_dir) if src else None,
    }


def section(lines, start, stops):
    # Course pages repeat the labels in an in-page tab bar first: the real heading is the last one.
    hits = [n for n, line in enumerate(lines) if line == start]
    if not hits:
        return []
    i = hits[-1]
    items = []
    for line in lines[i + 1:]:
        if line in stops:
            break
        if re.fullmatch(r"\d{1,2}", line):
            continue
        items.append(line)
    return items


def media_url(media_id, cache):
    if not media_id:
        return None
    if media_id in cache:
        return cache[media_id]
    try:
        m = json.loads(get(f"{BASE}/wp-json/wp/v2/media/{media_id}"))
        sizes = (m.get("media_details") or {}).get("sizes") or {}
        best = sizes.get("1536x1536") or sizes.get("large") or sizes.get("full") or {}
        url = best.get("source_url") or m.get("source_url")
        cache[media_id] = {"url": url, "alt": m.get("alt_text", "")}
    except Exception:
        cache[media_id] = None
    return cache[media_id]


def download(url, out_dir):
    if not url:
        return None
    name = os.path.basename(urllib.parse.urlparse(url).path)
    path = os.path.join(out_dir, name)
    if not os.path.exists(path):
        try:
            with open(path, "wb") as f:
                f.write(get(url, binary=True))
        except Exception:
            return None
    return name


def terms(tax):
    return {t["id"]: {"name": html.unescape(t["name"]), "slug": t["slug"]} for t in rest(tax)}


def faq_items(page_html):
    """Question/answer pairs from the live FAQ page (numbered list: number, question, answer)."""
    lines = text_lines(main_html(page_html))
    items = []
    for n, line in enumerate(lines):
        if re.fullmatch(r"\d{2}", line) and n + 2 < len(lines) and lines[n + 1].endswith("?"):
            items.append({"q": lines[n + 1], "a": lines[n + 2]})
    return items


def it_slug(page_html):
    """Slug of the Italian translation, from the hreflang alternates Polylang prints ('home' for the front page)."""
    m = re.search(r'<link rel="alternate" href="([^"]+)" hreflang="it"', page_html)
    if not m:
        return ""
    path = urllib.parse.urlparse(m.group(1)).path.strip("/")
    return path.rsplit("/", 1)[-1] if path else "home"


# Version of the page content format in data.json: setup.sh refreshes only the pages when it is older.
PAGES_FORMAT = 3


def fetch_pages(media_dir, lang):
    """Pages of one language: editorial pages as blocks with lead and photo, the others as plain semantic HTML."""
    pages = []
    all_pages = {p["id"]: p for p in rest("pages")}
    for p in all_pages.values():
        english = "/en/" in p.get("link", "") or urllib.parse.urlparse(p.get("link", "")).path.rstrip("/") == "/en"
        if english != (lang == "en") or p["slug"] in ("pagina-di-esempio",):
            continue
        try:
            page_html = get(p["link"])
        except Exception:
            page_html = ""
        content, excerpt, image = p["content"]["rendered"], "", None
        ed = editorial(page_html, media_dir) if page_html else None
        if ed:
            excerpt, image = ed["excerpt"], ed["image"]
        if ed and ed["content"]:
            content = ed["content"]
        elif len(content.strip()) < 40:
            content = to_blocks(main_html(page_html)) if page_html else ""
        parent = all_pages.get(p["parent"])
        item = {
            "id": p["id"], "title": html.unescape(p["title"]["rendered"]), "slug": p["slug"],
            "parent_slug": parent["slug"] if parent else "", "menu_order": p.get("menu_order", 0),
            "content": content, "excerpt": excerpt, "image": image, "link": p["link"],
        }
        if lang == "en":
            item["it_slug"] = it_slug(page_html) if page_html else ""
            item["front"] = urllib.parse.urlparse(p["link"]).path.rstrip("/") == "/en"
        pages.append(item)
    return pages


def run_en(out, tx, media_dir, cache):
    """English half of the live site, each item linked to its Italian translation (it_slug)."""
    def en(item):
        return "/en/" in item.get("link", "")

    home = get(BASE + "/en/")
    home_lines = text_lines(main_html(home))
    stops = {"Skills", "Your professional future", "Programme", "Tools and technologies", "Related courses", "Course teachers", "Teachers", "Talk to us"}
    courses = []
    for c in filter(en, rest("corso")):
        title = html.unescape(c["title"]["rendered"])
        page_html = get(c["link"])
        lines = text_lines(main_html(page_html))
        idx = [i for i, x in enumerate(lines) if x == title]
        intro = lines[idx[-1] + 1] if idx and idx[-1] + 1 < len(lines) else ""
        teaser = ""
        if title in home_lines:
            j = home_lines.index(title)
            teaser = home_lines[j + 1] if j + 1 < len(home_lines) else ""
        img = media_url(c.get("featured_media"), cache)
        courses.append({
            "title": title, "slug": c["slug"], "menu_order": c.get("menu_order", 0), "it_slug": it_slug(page_html),
            "area": [tx["area_tecnologica"][t]["name"] for t in c.get("area_tecnologica", []) if t in tx["area_tecnologica"]],
            "excerpt": teaser, "intro": intro,
            "competenze": section(lines, "Skills", stops),
            "professioni": section(lines, "Your professional future", stops),
            "programma": section(lines, "Programme", stops),
            "strumenti": section(lines, "Tools and technologies", stops),
            "image": download(img and img["url"], media_dir), "image_alt": img and img["alt"],
        })

    posts = []
    for p in filter(en, rest("posts")):
        img = media_url(p.get("featured_media"), cache) if len(posts) < 24 else None
        try:
            slug_it = it_slug(get(p["link"]))
        except Exception:
            slug_it = ""
        posts.append({
            "title": html.unescape(p["title"]["rendered"]), "slug": p["slug"], "date": p["date"], "it_slug": slug_it,
            "content": p["content"]["rendered"], "excerpt": re.sub(r"<[^>]+>", "", p["excerpt"]["rendered"]).strip(),
            "categories": [tx["categories"][t]["name"] for t in p.get("categories", []) if t in tx["categories"]],
            "image": download(img and img["url"], media_dir), "image_alt": img and img["alt"],
        })

    events = []
    for e in filter(en, rest("evento")):
        page_html = get(e["link"])
        lines = text_lines(main_html(page_html))
        def after(label):
            return lines[lines.index(label) + 1] if label in lines and lines.index(label) + 1 < len(lines) else ""
        title = html.unescape(e["title"]["rendered"])
        idx = [i for i, x in enumerate(lines) if x == title]
        img = media_url(e.get("featured_media"), cache)
        events.append({
            "title": title, "slug": e["slug"], "date": e["date"], "it_slug": it_slug(page_html),
            "description": lines[idx[-1] + 1] if idx and idx[-1] + 1 < len(lines) else "",
            "data_text": after("Date"), "orario": after("Time"), "modalita": after("Format"), "luogo": after("Location"),
            "tipo_evento": [tx["tipo_evento"][t]["name"] for t in e.get("tipo_evento", []) if t in tx["tipo_evento"]],
            "image": download(img and img["url"], media_dir), "image_alt": img and img["alt"],
        })

    docs = []
    for d in filter(en, rest("documento")):
        doc = {
            "title": html.unescape(d["title"]["rendered"]), "slug": d["slug"], "date": d["date"],
            "tipo": [tx["tipo"][t]["name"] for t in d.get("tipo", []) if t in tx["tipo"]],
            "biennio": [tx["biennio"][t]["name"] for t in d.get("biennio", []) if t in tx["biennio"]],
        }
        try:
            page_html = get(d["link"])
            m = re.search(r'href="([^"]+\.(?:pdf|docx?|xlsx?|zip))"', page_html)
            doc["file_url"] = m.group(1) if m else ""
            doc["it_slug"] = it_slug(page_html)
        except Exception:
            pass
        docs.append(doc)

    editions = [{
        "title": html.unescape(ed["title"]["rendered"]), "slug": ed["slug"], "date": ed["date"],
        "famiglia": [tx["famiglia"][t]["name"] for t in ed.get("famiglia", []) if t in tx["famiglia"]],
        "biennio": [tx["biennio"][t]["name"] for t in ed.get("biennio", []) if t in tx["biennio"]],
    } for ed in filter(en, rest("edizione_corso"))]

    pages = fetch_pages(media_dir, "en")

    return {
        "courses": courses, "posts": posts, "events": events, "documents": docs, "editions": editions,
        "pages": pages, "faq": faq_items(get(BASE + "/en/faq/")),
    }


def run(out, courses_only=False, faq_only=False, en_only=False, pages_only=False):
    media_dir = os.path.join(out, "media")
    os.makedirs(media_dir, exist_ok=True)
    cache = {}
    data_path = os.path.join(out, "data.json")
    if pages_only and os.path.exists(data_path):
        with open(data_path, encoding="utf-8") as f:
            previous = json.load(f)
        previous["pages"] = fetch_pages(media_dir, "it")
        previous["pages_format"] = PAGES_FORMAT
        if "en" in previous:
            previous["en"]["pages"] = fetch_pages(media_dir, "en")
        with open(data_path, "w", encoding="utf-8") as f:
            json.dump(previous, f, ensure_ascii=False, indent=1)
        print(f"pages: it={len(previous['pages'])} en={len(previous.get('en', {}).get('pages', []))} (refreshed)")
        return
    data = {"source": BASE, "taxonomies": {}}
    for tax in ("area_tecnologica", "tipo_evento", "famiglia", "biennio", "tipo", "categories"):
        data["taxonomies"][tax] = terms(tax)
    tx = data["taxonomies"]
    data_path = os.path.join(out, "data.json")
    if (courses_only or faq_only or en_only) and os.path.exists(data_path):
        with open(data_path, encoding="utf-8") as f:
            previous = json.load(f)
    if en_only and os.path.exists(data_path):
        with open(data_path, encoding="utf-8") as f:
            previous = json.load(f)
        previous["en"] = run_en(out, tx, media_dir, cache)
        with open(data_path, "w", encoding="utf-8") as f:
            json.dump(previous, f, ensure_ascii=False, indent=1)
        print("en: " + " ".join(f"{k}={len(v)}" for k, v in previous["en"].items()))
        return
    if faq_only and os.path.exists(data_path):
        previous["faq"] = faq_items(get(BASE + "/faq/"))
        with open(data_path, "w", encoding="utf-8") as f:
            json.dump(previous, f, ensure_ascii=False, indent=1)
        print(f"faq={len(previous['faq'])} (refreshed)")
        return

    # Courses: home teaser lines + structured sections from each course page.
    home = get(BASE + "/")
    home_lines = text_lines(main_html(home))
    courses = []
    for c in filter(is_it, rest("corso")):
        title = html.unescape(c["title"]["rendered"])
        page = main_html(get(c["link"]))
        lines = text_lines(page)
        stops = {"Competenze", "Il tuo futuro professionale", "Programma", "Strumenti e tecnologie", "Corsi correlati", "Docenti del corso", "Docenti", "Parla con noi"}
        intro = ""
        if title in lines:
            idx = [i for i, x in enumerate(lines) if x == title][-1]
            if idx + 1 < len(lines):
                intro = lines[idx + 1]
        teaser = ""
        if title in home_lines:
            j = home_lines.index(title)
            teaser = home_lines[j + 1] if j + 1 < len(home_lines) else ""
        img = media_url(c.get("featured_media"), cache)
        courses.append({
            "title": title, "slug": c["slug"], "menu_order": c.get("menu_order", 0),
            "area": [tx["area_tecnologica"][t]["name"] for t in c.get("area_tecnologica", []) if t in tx["area_tecnologica"]],
            "excerpt": teaser, "intro": intro,
            "competenze": section(lines, "Competenze", stops),
            "professioni": section(lines, "Il tuo futuro professionale", stops),
            "programma": section(lines, "Programma", stops),
            "strumenti": section(lines, "Strumenti e tecnologie", stops),
            "image": download(img and img["url"], media_dir), "image_alt": img and img["alt"],
        })
    data["courses"] = courses
    if courses_only and os.path.exists(data_path):
        previous["courses"] = courses
        with open(data_path, "w", encoding="utf-8") as f:
            json.dump(previous, f, ensure_ascii=False, indent=1)
        print(f"courses={len(courses)} (refreshed)")
        return

    posts = []
    for p in filter(is_it, rest("posts")):
        img = media_url(p.get("featured_media"), cache) if len(posts) < 24 else None
        posts.append({
            "title": html.unescape(p["title"]["rendered"]), "slug": p["slug"], "date": p["date"],
            "content": p["content"]["rendered"], "excerpt": re.sub(r"<[^>]+>", "", p["excerpt"]["rendered"]).strip(),
            "categories": [tx["categories"][t]["name"] for t in p.get("categories", []) if t in tx["categories"]],
            "image": download(img and img["url"], media_dir), "image_alt": img and img["alt"],
        })
    data["posts"] = posts

    events = []
    for e in rest("evento"):
        if "/en/" in e["link"]:
            continue
        lines = text_lines(main_html(get(e["link"])))
        def after(label):
            return lines[lines.index(label) + 1] if label in lines and lines.index(label) + 1 < len(lines) else ""
        title = html.unescape(e["title"]["rendered"])
        idx = [i for i, x in enumerate(lines) if x == title]
        desc = lines[idx[-1] + 1] if idx and idx[-1] + 1 < len(lines) else ""
        img = media_url(e.get("featured_media"), cache)
        events.append({
            "title": title, "slug": e["slug"], "date": e["date"], "description": desc,
            "data_text": after("Data"), "orario": after("Orario"), "modalita": after("Modalità"), "luogo": after("Luogo"),
            "tipo_evento": [tx["tipo_evento"][t]["name"] for t in e.get("tipo_evento", []) if t in tx["tipo_evento"]],
            "image": download(img and img["url"], media_dir), "image_alt": img and img["alt"],
        })
    data["events"] = events

    docs = []
    for d in rest("documento"):
        if "/en/" in d["link"]:
            continue
        docs.append({
            "title": html.unescape(d["title"]["rendered"]), "slug": d["slug"], "date": d["date"],
            "tipo": [tx["tipo"][t]["name"] for t in d.get("tipo", []) if t in tx["tipo"]],
            "biennio": [tx["biennio"][t]["name"] for t in d.get("biennio", []) if t in tx["biennio"]],
            "link": d["link"],
        })
    # File URLs for the first documents (enough for preview).
    for doc in docs[:40]:
        try:
            page = get(doc["link"])
            m = re.search(r'href="([^"]+\.(?:pdf|docx?|xlsx?|zip))"', page)
            doc["file_url"] = m.group(1) if m else ""
            lines = text_lines(main_html(page))
            doc["formato"] = lines[lines.index("Formato") + 1] if "Formato" in lines else ""
            doc["dimensione"] = lines[lines.index("Dimensione") + 1] if "Dimensione" in lines else ""
        except Exception:
            pass
    data["documents"] = docs

    editions = []
    for ed in rest("edizione_corso"):
        if "/en/" in ed["link"]:
            continue
        editions.append({
            "title": html.unescape(ed["title"]["rendered"]), "slug": ed["slug"], "date": ed["date"],
            "famiglia": [tx["famiglia"][t]["name"] for t in ed.get("famiglia", []) if t in tx["famiglia"]],
            "biennio": [tx["biennio"][t]["name"] for t in ed.get("biennio", []) if t in tx["biennio"]],
        })
    data["editions"] = editions

    data["pages"] = fetch_pages(media_dir, "it")
    data["pages_format"] = PAGES_FORMAT
    data["faq"] = faq_items(get(BASE + "/faq/"))

    partners = []
    for m in re.finditer(r'<li class="ros-logo-item[^"]*">(?:<a href="([^"]+)">)?<img[^>]+src="([^"]+)"[^>]*alt="([^"]*)"', home):
        partners.append({"url": m.group(1) or "", "image": download(m.group(2), media_dir), "name": html.unescape(m.group(3))})
    data["partners"] = partners
    data["en"] = run_en(out, tx, media_dir, cache)

    with open(os.path.join(out, "data.json"), "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=1)
    print(f"courses={len(courses)} posts={len(posts)} events={len(events)} documents={len(docs)} "
          f"editions={len(editions)} pages={len(pages)} partners={len(partners)}")


if __name__ == "__main__":
    ap = argparse.ArgumentParser()
    ap.add_argument("--out", default=os.path.join(os.path.dirname(__file__), "..", "..", ".wp", "seed"))
    ap.add_argument("--courses-only", action="store_true", help="refresh only the courses in an existing data.json")
    ap.add_argument("--faq-only", action="store_true", help="refresh only the FAQ in an existing data.json")
    ap.add_argument("--en-only", action="store_true", help="add or refresh only the English content in an existing data.json")
    ap.add_argument("--pages-only", action="store_true", help="refresh only the pages (both languages) in an existing data.json")
    args = ap.parse_args()
    run(os.path.abspath(args.out), courses_only=args.courses_only, faq_only=args.faq_only, en_only=args.en_only,
        pages_only=args.pages_only)
