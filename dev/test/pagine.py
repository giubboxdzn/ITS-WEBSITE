"""Every kind of page, in Italian and English.

The pages are found on the site itself (menus, first course, event, document and article), so the list follows
the content. For each one: the expected status, no PHP error or warning in the HTML, no JavaScript error,
one h1, the page language, a title, images with an alt attribute (empty is fine for decorative ones),
"Vai al contenuto" as the first stop of the keyboard, and no sideways scrolling on a 320px phone.
"""
import re
from urllib.parse import urlsplit

TITLE = "ogni tipo di pagina, in italiano e in inglese: risposta, errori PHP e JavaScript, accessibilità di base, telefono"

PHP_ERROR = re.compile(
    r"<b>(?:Fatal error|Parse error|Warning|Notice|Deprecated)</b>:|"
    r"(?:Fatal error|Parse error|Warning|Notice|Deprecated): .{0,300}? on line \d+"
)
# First link of each kind, looked for on these pages (Italian, then English).
KINDS = [
    ("scheda corso", r"/corso/[^/]+/$", ["/offerta-formativa/corsi/", "/"]),
    ("evento", r"/event/[^/]+/$", ["/eventi/", "/"]),
    ("documento", r"/documento/[^/]+/$", ["/bandi-albi/", "/bandi/"]),
    ("articolo", None, ["/news/"]),
    ("course page", r"/en/corso/[^/]+/$", ["/en/"]),
]
MAX_MENU = 24


def same_site(t, href):
    parts = urlsplit(href)
    return parts.scheme in ("http", "https") and t.base.endswith(parts.netloc) and not parts.path.startswith("/wp-")


def links(page, selector):
    return page.eval_on_selector_all(selector, "els => els.map(a => a.href.split('#')[0])")


def discover(t):
    page = t.browser.new_page()
    pages = {"/": "home", "/en/": "home inglese", "/?s=corso": "ricerca", "/en/?s=course": "ricerca inglese",
             "/pagina-che-non-esiste-test/": "pagina non trovata"}
    for start in ("/", "/en/"):
        page.goto(t.url(start))
        for href in links(page, "nav a[href]"):
            path = urlsplit(href).path or "/"
            if same_site(t, href) and path not in pages and len(pages) < MAX_MENU:
                pages[path] = "menu"
    for label, pattern, where in KINDS:
        found = None
        for start in where:
            page.goto(t.url(start))
            candidates = links(page, ".news-card a[href]") if pattern is None else links(page, "a[href]")
            for href in candidates:
                path = urlsplit(href).path
                if same_site(t, href) and (pattern is None or re.search(pattern, path)):
                    found = path
                    break
            if found:
                break
        if found:
            pages[found] = label
        else:
            t.skip(label, "nessun esempio trovato nei contenuti locali")
    page.close()
    return pages


def check_page(t, desktop, phone, path, label):
    errors = []
    desktop.on("pageerror", lambda e: errors.append(str(e)))
    response = desktop.goto(t.url(path), wait_until="load")
    expected = 404 if label == "pagina non trovata" else 200
    status = response.status if response else 0
    html = desktop.content()
    php = PHP_ERROR.search(html)
    facts = desktop.evaluate(
        """() => ({
            h1: document.querySelectorAll('h1').length,
            lang: document.documentElement.lang,
            title: document.title.trim(),
            noAlt: [...document.images].filter(i => !i.hasAttribute('alt')).map(i => i.src.split('/').pop()).slice(0, 3),
        })"""
    )
    desktop.keyboard.press("Tab")
    first = desktop.evaluate("() => document.activeElement && document.activeElement.className")
    phone.goto(t.url(path), wait_until="load")
    overflow = phone.evaluate("() => document.documentElement.scrollWidth - document.documentElement.clientWidth")
    wanted_lang = "en" if path.startswith("/en/") else "it"
    problems = []
    if status != expected:
        problems.append(f"stato {status} invece di {expected}")
    if php:
        problems.append(f"errore PHP nella pagina: «{php.group(0)[:120]}»")
    if errors:
        problems.append(f"errore JavaScript: {errors[0][:160]}")
    if facts["h1"] != 1:
        problems.append(f"{facts['h1']} titoli h1 invece di 1")
    if not facts["lang"].startswith(wanted_lang):
        problems.append(f"lingua «{facts['lang']}» invece di {wanted_lang}")
    if not facts["title"]:
        problems.append("titolo della pagina vuoto")
    if facts["noAlt"]:
        problems.append("immagini senza attributo alt: " + ", ".join(facts["noAlt"]))
    if "skip-link" not in (first or ""):
        problems.append(f"il primo Tab va su «{first}», non su «Vai al contenuto»")
    if overflow > 1:
        problems.append(f"a 320 px la pagina scorre di lato di {overflow} px")
    if problems:
        t.shot(desktop, "pagina-" + re.sub(r"[^a-z0-9]+", "-", path.lower()).strip("-") or "home")
    t.check(not problems, f"{path}  ({label})", "; ".join(problems))


def run(t):
    pages = discover(t)
    desktop_ctx = t.browser.new_context(viewport={"width": 1280, "height": 900})
    phone_ctx = t.browser.new_context(viewport={"width": 320, "height": 640}, device_scale_factor=1)
    for path, label in pages.items():
        desktop = desktop_ctx.new_page()
        phone = phone_ctx.new_page()
        check_page(t, desktop, phone, path, label)
        desktop.close()
        phone.close()
    desktop_ctx.close()
    phone_ctx.close()

    # Header and footer links of both home pages lead somewhere.
    page = t.browser.new_page()
    broken = []
    seen = set()
    for start in ("/", "/en/"):
        page.goto(t.url(start))
        for href in links(page, "header a[href], footer a[href]"):
            if not same_site(t, href) or href in seen:
                continue
            seen.add(href)
            status = page.request.get(href, max_redirects=5).status
            if status >= 400:
                broken.append(f"{urlsplit(href).path} ({status})")
    page.close()
    t.check(not broken, f"link di header e footer ({len(seen)}) raggiungibili", ", ".join(broken[:8]))
