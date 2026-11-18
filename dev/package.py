#!/usr/bin/env python3
"""Production package of the theme: dist/its-rossellini-<version>.zip, ready for Appearance → Themes → Upload.

The zip is built from the last commit (git archive), so it holds exactly what is in the repository: no local
WordPress, no dev tooling, no files that were never committed. Before building, it checks that:

1. the theme folder has no uncommitted changes (they would not be in the zip);
2. the version in style.css matches the "Stable tag" and a changelog entry in readme.txt;
3. the translations are compiled and up to date (dev/i18n/build.py run on a copy gives the same files);
4. every PHP file passes `php -l` and main.js passes `node --check` (when PHP and Node are installed).

Then it checks the zip itself (one its-rossellini/ folder, required files, no stray files) and writes its SHA-256.

Usage: python3 -I dev/package.py
"""
import hashlib
import os
import re
import shutil
import subprocess
import sys
import tempfile
import zipfile

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
SLUG = "its-rossellini"
THEME_REL = f"wp-content/themes/{SLUG}"
THEME = os.path.join(ROOT, *THEME_REL.split("/"))
DIST = os.path.join(ROOT, "dist")
REQUIRED = ["style.css", "functions.php", "index.php", "screenshot.png", "readme.txt"]
STRAY = re.compile(r"(^|/)(\.git\w*|\.DS_Store|Thumbs\.db|desktop\.ini|node_modules|__pycache__|\.env[^/]*)(/|$)|\.(log|bak|orig|swp|tmp)$", re.I)


def fail(message):
    sys.stdout.flush()
    print(f"\n✗ {message}", file=sys.stderr)
    sys.exit(1)


def ok(message):
    print(f"✓ {message}")


def run(args, **kwargs):
    return subprocess.run(args, cwd=ROOT, capture_output=True, text=True, **kwargs)


def header(path, field):
    with open(path, encoding="utf-8") as f:
        m = re.search(rf"^\s*{re.escape(field)}:\s*(.+?)\s*$", f.read(), re.M)
    return m.group(1) if m else ""


def check_clean():
    status = run(["git", "status", "--porcelain", "--", THEME_REL])
    if status.returncode != 0:
        fail("git non risponde: lanciare lo script dalla cartella del repository.")
    if status.stdout.strip():
        fail("Il tema ha modifiche non committate, che non finirebbero nello zip:\n" + status.stdout.rstrip()
             + "\nFare commit (o annullarle) e rilanciare.")
    ok("tema senza modifiche non committate")


def check_version():
    version = header(os.path.join(THEME, "style.css"), "Version")
    if not re.fullmatch(r"\d+\.\d+\.\d+", version):
        fail(f"Version in style.css non valida: «{version}».")
    readme = os.path.join(THEME, "readme.txt")
    stable = header(readme, "Stable tag")
    if stable != version:
        fail(f"style.css dice {version}, readme.txt (Stable tag) dice {stable}: allinearli.")
    with open(readme, encoding="utf-8") as f:
        if not re.search(rf"^= {re.escape(version)}\b", f.read(), re.M):
            fail(f"Nel changelog di readme.txt manca la voce «= {version} …».")
    for field in ("Requires at least", "Requires PHP"):
        if not header(os.path.join(THEME, "style.css"), field):
            fail(f"Manca «{field}» in style.css.")
    ok(f"versione {version} (style.css, readme.txt, changelog)")
    return version


def check_translations():
    with tempfile.TemporaryDirectory() as tmp:
        os.makedirs(os.path.join(tmp, "dev", "i18n"))
        shutil.copy2(os.path.join(ROOT, "dev", "i18n", "build.py"), os.path.join(tmp, "dev", "i18n", "build.py"))
        copy = os.path.join(tmp, *THEME_REL.split("/"))
        shutil.copytree(THEME, copy)
        done = subprocess.run([sys.executable, "-I", os.path.join(tmp, "dev", "i18n", "build.py")], capture_output=True, text=True)
        if done.returncode != 0:
            fail("dev/i18n/build.py non è andato a buon fine:\n" + done.stderr)
        stale = []
        for name in sorted(os.listdir(os.path.join(THEME, "languages"))):
            with open(os.path.join(THEME, "languages", name), "rb") as a, open(os.path.join(copy, "languages", name), "rb") as b:
                if a.read() != b.read():
                    stale.append(name)
        if stale:
            fail("Traduzioni non aggiornate (" + ", ".join(stale) + "): lanciare python3 -I dev/i18n/build.py, completare en_GB.po e fare commit.")
        missing = re.search(r"(\w+): (\d+)/(\d+) translated", done.stdout)
        if missing and missing.group(2) != missing.group(3):
            print(f"! {missing.group(1)}: {missing.group(2)} testi tradotti su {missing.group(3)}; gli altri restano in italiano.")
    ok("traduzioni compilate e aggiornate")


def check_code():
    php = shutil.which("php")
    if php:
        errors = []
        for folder, _, files in os.walk(THEME):
            for name in files:
                if name.endswith(".php"):
                    path = os.path.join(folder, name)
                    lint = subprocess.run([php, "-l", path], capture_output=True, text=True)
                    if lint.returncode != 0:
                        errors.append(lint.stdout.strip() or lint.stderr.strip())
        if errors:
            fail("Errori di sintassi PHP:\n" + "\n".join(errors))
        ok("sintassi PHP")
    else:
        print("! PHP non trovato: salto il controllo della sintassi PHP.")
    node = shutil.which("node")
    if node:
        js = os.path.join(THEME, "assets", "js", "main.js")
        lint = subprocess.run([node, "--check", js], capture_output=True, text=True)
        if lint.returncode != 0:
            fail("Errore di sintassi in main.js:\n" + lint.stderr)
        ok("sintassi JavaScript")
    else:
        print("! Node non trovato: salto il controllo di main.js.")


def build(version):
    os.makedirs(DIST, exist_ok=True)
    target = os.path.join(DIST, f"{SLUG}-{version}.zip")
    if os.path.exists(target):
        os.remove(target)
    done = run(["git", "archive", "--format=zip", f"--prefix={SLUG}/", "-o", target, f"HEAD:{THEME_REL}"])
    if done.returncode != 0:
        fail("git archive non è riuscito:\n" + done.stderr)
    return target


def verify(target, version):
    with zipfile.ZipFile(target) as z:
        names = [n for n in z.namelist() if not n.endswith("/")]
        outside = [n for n in names if not n.startswith(f"{SLUG}/")]
        if outside:
            fail("File fuori dalla cartella del tema: " + ", ".join(outside[:5]))
        inside = {n[len(SLUG) + 1:] for n in names}
        absent = [n for n in REQUIRED if n not in inside]
        if absent:
            fail("Mancano nello zip: " + ", ".join(absent))
        stray = [n for n in names if STRAY.search(n)]
        if stray:
            fail("File da non pubblicare nello zip: " + ", ".join(stray[:10]))
        style = z.read(f"{SLUG}/style.css").decode("utf-8")
        if f"Version: {version}" not in style:
            fail("Lo style.css nello zip non ha la versione attesa.")
        broken = z.testzip()
        if broken:
            fail(f"Zip danneggiato: {broken}")
    digest = hashlib.sha256()
    with open(target, "rb") as f:
        for chunk in iter(lambda: f.read(1 << 16), b""):
            digest.update(chunk)
    with open(target + ".sha256", "w", encoding="utf-8", newline="\n") as f:
        f.write(f"{digest.hexdigest()}  {os.path.basename(target)}\n")
    size = os.path.getsize(target)
    ok(f"zip verificato: {len(names)} file, {size / 1024 / 1024:.1f} MB, una sola cartella {SLUG}/")
    return digest.hexdigest()


def main():
    print(f"Pacchetto di produzione del tema {SLUG}\n")
    check_clean()
    version = check_version()
    check_translations()
    check_code()
    target = build(version)
    digest = verify(target, version)
    commit = run(["git", "rev-parse", "--short", "HEAD"]).stdout.strip()
    rel = os.path.relpath(target, ROOT)
    print(f"""
Pronto: {rel}
  versione {version}, dal commit {commit}
  SHA-256  {digest}  ({rel}.sha256)

Per installarlo: WordPress → Aspetto → Temi → Aggiungi nuovo tema → Carica tema.
Requisiti, plugin e passaggi dopo l'installazione: readme.txt nello zip e README.md, «Messa in produzione».""")


if __name__ == "__main__":
    main()
