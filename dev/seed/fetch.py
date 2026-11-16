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


def run(out, courses_only=False, faq_only=False):
    media_dir = os.path.join(out, "media")
    os.makedirs(media_dir, exist_ok=True)
    cache = {}
    data = {"source": BASE, "taxonomies": {}}
    for tax in ("area_tecnologica", "tipo_evento", "famiglia", "biennio", "tipo", "categories"):
        data["taxonomies"][tax] = terms(tax)
    tx = data["taxonomies"]
    data_path = os.path.join(out, "data.json")
    if (courses_only or faq_only) and os.path.exists(data_path):
        with open(data_path, encoding="utf-8") as f:
            previous = json.load(f)
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

    pages = []
    all_pages = {p["id"]: p for p in rest("pages")}
    for p in all_pages.values():
        if not is_it(p) or p["slug"] in ("pagina-di-esempio",):
            continue
        content = p["content"]["rendered"]
        if len(content.strip()) < 40:
            try:
                content = to_blocks(main_html(get(p["link"])))
            except Exception:
                content = ""
        parent = all_pages.get(p["parent"])
        pages.append({
            "id": p["id"], "title": html.unescape(p["title"]["rendered"]), "slug": p["slug"],
            "parent_slug": parent["slug"] if parent else "", "menu_order": p.get("menu_order", 0),
            "content": content, "link": p["link"],
        })
    data["pages"] = pages
    data["faq"] = faq_items(get(BASE + "/faq/"))

    partners = []
    for m in re.finditer(r'<li class="ros-logo-item[^"]*">(?:<a href="([^"]+)">)?<img[^>]+src="([^"]+)"[^>]*alt="([^"]*)"', home):
        partners.append({"url": m.group(1) or "", "image": download(m.group(2), media_dir), "name": html.unescape(m.group(3))})
    data["partners"] = partners

    with open(os.path.join(out, "data.json"), "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=1)
    print(f"courses={len(courses)} posts={len(posts)} events={len(events)} documents={len(docs)} "
          f"editions={len(editions)} pages={len(pages)} partners={len(partners)}")


if __name__ == "__main__":
    ap = argparse.ArgumentParser()
    ap.add_argument("--out", default=os.path.join(os.path.dirname(__file__), "..", "..", ".wp", "seed"))
    ap.add_argument("--courses-only", action="store_true", help="refresh only the courses in an existing data.json")
    ap.add_argument("--faq-only", action="store_true", help="refresh only the FAQ in an existing data.json")
    args = ap.parse_args()
    run(os.path.abspath(args.out), courses_only=args.courses_only, faq_only=args.faq_only)
