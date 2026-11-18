"""Scroll animations (DESIGN.md, Motion).

Going down things come in; going back up what sinks into the bottom quarter goes out, in view, with the same
transitions; at rest nothing on screen stays hidden; with the switch off (or reduced motion) nothing is hidden.
"""

TITLE = "animazioni allo scroll: entrata, uscita risalendo, riposo, interruttore spento"

STATE = """() => {
    const vh = innerHeight;
    const all = [...document.querySelectorAll('[data-reveal]')].filter((e) => e.offsetParent);
    const top = (e) => e.getBoundingClientRect().top;
    const shown = (e) => e.classList.contains('is-in');
    return {
        total: all.length,
        hiddenInView: all.filter((e) => top(e) < vh * 0.9 && e.getBoundingClientRect().bottom > 0 && !shown(e)).length,
        below: all.filter((e) => top(e) > vh).length,
        hiddenBelow: all.filter((e) => top(e) > vh && !shown(e)).length,
        hiddenAbove: all.filter((e) => e.getBoundingClientRect().bottom < 0 && !shown(e)).length,
        figures: [...document.querySelectorAll('.tile__num .count')].map((n) => n.textContent),
        mark: document.querySelector('.colophon__mark')?.classList.contains('is-in') ?? null,
    };
}"""


def wheel(page, total, step):
    moved = 0
    while abs(moved) < abs(total):
        page.mouse.wheel(0, step)
        page.wait_for_timeout(60)
        moved += step


def run(t):
    before = len(t.failed)
    context = t.browser.new_context(viewport={"width": 1280, "height": 800}, reduced_motion="no-preference")
    page = context.new_page()
    errors = []
    page.on("pageerror", lambda e: errors.append(str(e)))
    page.goto(t.url("/"), wait_until="load")
    page.wait_for_timeout(2500)
    s = page.evaluate(STATE)
    t.check(page.evaluate("() => document.documentElement.classList.contains('anim')"), "animazioni attive di serie")
    t.check(s["total"] > 20, f"elementi animati trovati ({s['total']})")
    t.check(s["hiddenInView"] == 0, "all'apertura nulla è nascosto sullo schermo", str(s))
    t.check(s["hiddenBelow"] == s["below"] and s["below"] > 0, "sotto lo schermo gli elementi aspettano di entrare", str(s))

    height = page.evaluate("() => document.documentElement.scrollHeight")
    wheel(page, height, 300)
    page.wait_for_timeout(2600)
    s = page.evaluate(STATE)
    t.check(s["hiddenInView"] == 0 and s["hiddenAbove"] == 0, "scendendo tutto entra, e ciò che esce dall'alto resta", str(s))
    t.check(s["mark"] is True, "in fondo il logo del footer è disegnato", str(s))
    t.check(s["figures"] and all(not f.startswith("0") for f in s["figures"]), f"i numeri hanno contato fino al valore ({', '.join(s['figures'])})")

    # Going up: an element pushed into the bottom quarter goes out while still on screen, with the entry transitions.
    probe = page.evaluate("""() => {
        const el = [...document.querySelectorAll('[data-reveal="done"]')]
            .find((e) => { const r = e.getBoundingClientRect(); return r.top > innerHeight * 0.3 && r.top < innerHeight * 0.6 && !e.querySelector('.word'); });
        if (!el) return null;
        el.setAttribute('data-test-probe', '');
        return Math.round(el.getBoundingClientRect().top);
    }""")
    if probe is None:
        t.skip("uscita in vista", "nessun elemento adatto a metà schermo")
    else:
        page.mouse.wheel(0, -(int(800 * 0.85) - probe))
        page.wait_for_timeout(250)
        out = page.evaluate("""() => {
            const e = document.querySelector('[data-test-probe]');
            const cs = getComputedStyle(e);
            return { top: e.getBoundingClientRect().top, isIn: e.classList.contains('is-in'), duration: cs.transitionDuration, vh: innerHeight };
        }""")
        visible = out["vh"] - out["top"]
        t.check(not out["isIn"] and visible > 40, f"risalendo un elemento esce mentre è ancora visibile ({int(visible)} px sullo schermo)", str(out))
        t.check(out["duration"] == "0.8s, 1s", f"in uscita la stessa durata dell'entrata ({out['duration']})")

    wheel(page, height, -300)
    page.wait_for_timeout(2600)
    s = page.evaluate(STATE)
    t.check(s["hiddenBelow"] == s["below"], "tornati in cima, tutto ciò che sta sotto è pronto a rientrare", str(s))
    t.check(s["hiddenInView"] == 0, "a riposo nulla resta nascosto sullo schermo", str(s))
    t.check(s["figures"] and all(f.startswith("0") for f in s["figures"]), f"i numeri sono tornati a zero ({', '.join(s['figures'])})")
    t.check(s["mark"] is False, "il logo del footer si è cancellato", str(s))

    wheel(page, height / 2, 300)
    page.wait_for_timeout(2600)
    s = page.evaluate(STATE)
    t.check(s["hiddenInView"] == 0, "riscendendo le animazioni ripartono", str(s))
    t.check(not errors, "nessun errore JavaScript", "; ".join(errors[:3]))
    if len(t.failed) > before:
        t.shot(page, "animazioni")
    context.close()

    # Reduced motion and no choice in the footer: a static page.
    context = t.browser.new_context(viewport={"width": 1280, "height": 800}, reduced_motion="reduce")
    page = context.new_page()
    page.goto(t.url("/"), wait_until="load")
    wheel(page, 3000, 400)
    page.wait_for_timeout(500)
    static = page.evaluate("""() => ({
        off: document.documentElement.classList.contains('motion-off'),
        anim: document.documentElement.classList.contains('anim'),
        hidden: [...document.querySelectorAll('[data-reveal]')].filter((e) => !e.classList.contains('is-in')).length,
    })""")
    t.check(static["off"] and not static["anim"] and static["hidden"] == 0, "con «riduci movimento» il sito è statico e nulla è nascosto", str(static))
    context.close()
