#!/usr/bin/env python3
"""Tests of the theme on the local site, in a real browser (Playwright with Chromium).

    python3 -I dev/test/run.py --installa   # once: .wp/venv with Playwright and Chromium (about 150 MB)
    python3 -I dev/test/run.py              # all the tests
    python3 -I dev/test/run.py pagine admin # only some: pagine, animazioni, admin, pacchetto
    python3 -I dev/test/run.py --url http://localhost:8080   # against a local site already running

It needs the local WordPress (dev/setup.sh or one of the avvia-* launchers, once). It starts its own PHP server
on a free port and stops it at the end. The admin test deactivates Polylang and activates it again, in the local
database only. Nothing is sent to the live site. Screenshots of what failed go to .wp/test-results/.
"""
import importlib.util
import os
import shutil
import socket
import subprocess
import sys
import time
import urllib.request
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
HERE = Path(__file__).resolve().parent
VENV = ROOT / ".wp" / "venv"
RESULTS = ROOT / ".wp" / "test-results"
SUITES = ["pagine", "animazioni", "admin", "pacchetto"]

try:
    sys.stdout.reconfigure(encoding="utf-8", errors="replace")
except (AttributeError, ValueError):
    pass


def venv_python():
    return VENV / ("Scripts/python.exe" if os.name == "nt" else "bin/python")


def ensure_playwright(install):
    try:
        import playwright.sync_api  # noqa: F401
        return
    except ImportError:
        pass
    py = venv_python()
    in_venv = Path(sys.prefix).resolve() == VENV.resolve()
    if py.exists() and not in_venv:
        # Run again with the project's own environment, where Playwright is installed.
        args = [a for a in sys.argv[1:] if a != "--installa"]
        sys.exit(subprocess.call([str(py), "-I", str(Path(__file__).resolve()), *args]))
    if not install:
        print("Playwright non è installato. Una volta sola:\n\n    python3 -I dev/test/run.py --installa\n")
        sys.exit(2)
    print("Preparo .wp/venv con Playwright e Chromium (una volta sola, qualche minuto)...")
    if not py.exists():
        subprocess.check_call([sys.executable, "-m", "venv", str(VENV)])
    subprocess.check_call([str(py), "-m", "pip", "install", "--quiet", "-r", str(HERE / "requirements.txt")])
    subprocess.check_call([str(py), "-m", "playwright", "install", "chromium"])
    args = [a for a in sys.argv[1:] if a != "--installa"]
    sys.exit(subprocess.call([str(py), "-I", str(Path(__file__).resolve()), *args]))


def free_port(start=8765):
    for port in range(start, start + 100):
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
            try:
                s.bind(("127.0.0.1", port))
                return port
            except OSError:
                continue
    raise SystemExit("Nessuna porta libera per il server dei test.")


def start_server():
    wordpress = ROOT / ".wp" / "wordpress"
    if not (wordpress / "wp-config.php").exists() or not (ROOT / ".wp" / "db" / "its.sqlite").exists():
        raise SystemExit("Manca il WordPress locale: prima dev/setup.sh, oppure uno dei file avvia-*.")
    php = shutil.which("php")
    if not php:
        raise SystemExit("PHP non trovato nel PATH.")
    port = free_port()
    base = f"http://127.0.0.1:{port}"
    RESULTS.mkdir(parents=True, exist_ok=True)
    log = open(RESULTS / "server.log", "w", encoding="utf-8")
    proc = subprocess.Popen(
        [php, "-d", "expose_php=0", "-S", f"127.0.0.1:{port}", "-t", str(wordpress), str(ROOT / "dev" / "router.php")],
        cwd=str(ROOT), env=dict(os.environ, WP_HOME=base), stdout=log, stderr=subprocess.STDOUT,
    )
    for _ in range(60):
        try:
            urllib.request.urlopen(base + "/", timeout=2)
            return proc, base
        except Exception:
            time.sleep(0.25)
    proc.terminate()
    raise SystemExit("Il server dei test non risponde: vedi .wp/test-results/server.log")


class Run:
    """What a suite gets: the site address, the browser, and check() to record each result."""

    def __init__(self, base, browser):
        self.base = base.rstrip("/")
        self.browser = browser
        self.passed = 0
        self.failed = []

    def url(self, path):
        return self.base + path

    def check(self, ok, label, detail=""):
        if ok:
            self.passed += 1
            print(f"  ✓ {label}")
        else:
            self.failed.append(label)
            print(f"  ✗ {label}" + (f"\n      {detail}" if detail else ""))
        return ok

    def skip(self, label, why):
        print(f"  – {label}: {why}")

    def shot(self, page, name):
        RESULTS.mkdir(parents=True, exist_ok=True)
        path = RESULTS / f"{name}.png"
        page.screenshot(path=str(path))
        return path.relative_to(ROOT)


def load(name):
    spec = importlib.util.spec_from_file_location(f"its_test_{name}", HERE / f"{name}.py")
    module = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(module)
    return module


def main():
    args = sys.argv[1:]
    install = "--installa" in args
    base = None
    if "--url" in args:
        i = args.index("--url")
        base = args[i + 1] if i + 1 < len(args) else None
        del args[i:i + 2]
    chosen = [a for a in args if not a.startswith("--")] or SUITES
    unknown = [a for a in chosen if a not in SUITES]
    if unknown:
        raise SystemExit(f"Serie sconosciuta: {', '.join(unknown)}. Disponibili: {', '.join(SUITES)}.")
    ensure_playwright(install)
    from playwright.sync_api import sync_playwright

    proc = None
    if not base:
        proc, base = start_server()
    print(f"Test sul sito locale {base}\n")
    started = time.time()
    try:
        with sync_playwright() as p:
            browser = p.chromium.launch()
            run = Run(base, browser)
            for name in chosen:
                module = load(name)
                print(f"{name} — {getattr(module, 'TITLE', '')}")
                try:
                    module.run(run)
                except Exception as error:  # a broken suite is a failure, the others still run
                    run.check(False, f"{name}: la serie si è interrotta", repr(error))
                print()
            browser.close()
    finally:
        if proc:
            proc.terminate()
            try:
                proc.wait(timeout=5)
            except subprocess.TimeoutExpired:
                proc.kill()
    seconds = time.time() - started
    if run.failed:
        print(f"✗ {len(run.failed)} controlli falliti, {run.passed} riusciti ({seconds:.0f} s). Schermate in .wp/test-results/.")
        sys.exit(1)
    print(f"✓ Tutti i {run.passed} controlli riusciti ({seconds:.0f} s).")


if __name__ == "__main__":
    main()
