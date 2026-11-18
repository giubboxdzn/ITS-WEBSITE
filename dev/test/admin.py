"""The admin: the theme's plugin notice (inc/requirements.php).

With every plugin active there is no notice; with Polylang deactivated the notice offers "Attiva", and the button
activates it again. Polylang is always active again at the end, also when a check fails.
"""

TITLE = "admin: avviso dei plugin necessari e pulsante «Attiva»"

NOTICE = ".its-requirements"


def notice_text(page):
    element = page.query_selector(NOTICE)
    return " ".join(element.inner_text().split()) if element else ""


def polylang_active(page):
    page.goto(page.url.split("/wp-admin/")[0] + "/wp-admin/plugins.php")
    row = page.query_selector('tr[data-slug="polylang"]')
    return bool(row and "active" in (row.get_attribute("class") or "").split())


def run(t):
    before = len(t.failed)
    context = t.browser.new_context(viewport={"width": 1280, "height": 900})
    page = context.new_page()
    errors = []
    page.on("pageerror", lambda e: errors.append(str(e)))
    page.goto(t.url("/wp-login.php"))
    page.fill("#user_login", "admin")
    page.fill("#user_pass", "admin")
    with page.expect_navigation():
        page.click("#wp-submit")
    if not t.check("/wp-admin" in page.url, "accesso all'admin (admin / admin)", page.url):
        context.close()
        return

    page.goto(t.url("/wp-admin/themes.php"))
    t.check(not notice_text(page), "con tutti i plugin attivi nessun avviso", notice_text(page))
    if not polylang_active(page):
        t.skip("prova con Polylang disattivato", "Polylang non è attivo nel sito locale")
        context.close()
        return
    try:
        page.click('tr[data-slug="polylang"] .deactivate a')
        page.wait_for_load_state("load")
        page.goto(t.url("/wp-admin/themes.php"))
        text = notice_text(page)
        t.check("Polylang" in text and page.query_selector(f"{NOTICE} a.button"), "senza Polylang l'avviso lo segnala con «Attiva»", text)
        button = page.query_selector(f"{NOTICE} a.button")
        if button:
            with page.expect_navigation():
                button.click()
        t.check(polylang_active(page), "il pulsante «Attiva» riattiva Polylang")
        page.goto(t.url("/wp-admin/themes.php"))
        t.check(not notice_text(page), "dopo l'attivazione l'avviso sparisce", notice_text(page))
    finally:
        if not polylang_active(page):
            page.click('tr[data-slug="polylang"] .activate a')
            page.wait_for_load_state("load")
    if len(t.failed) > before:
        t.shot(page, "admin")
    context.close()
