"""The production package (dev/package.py).

When the theme has no uncommitted changes, it builds the zip and checks the result; otherwise it says so and
skips, because the zip comes from the last commit.
"""
import subprocess
import sys
import zipfile
from pathlib import Path

TITLE = "pacchetto di produzione: zip del tema creato e verificato"

ROOT = Path(__file__).resolve().parents[2]


def run(t):
    dirty = subprocess.run(["git", "status", "--porcelain", "--", "wp-content/themes/its-rossellini"],
                           cwd=ROOT, capture_output=True, text=True).stdout.strip()
    if dirty:
        t.skip("pacchetto", "il tema ha modifiche non committate (lo zip si crea dall'ultimo commit)")
        return
    done = subprocess.run([sys.executable, "-I", str(ROOT / "dev" / "package.py")], cwd=ROOT, capture_output=True, text=True)
    if not t.check(done.returncode == 0, "dev/package.py termina senza errori", (done.stderr or done.stdout).strip()[-400:]):
        return
    zips = sorted((ROOT / "dist").glob("its-rossellini-*.zip"), key=lambda p: p.stat().st_mtime)
    if not t.check(bool(zips), "lo zip è in dist/"):
        return
    target = zips[-1]
    with zipfile.ZipFile(target) as z:
        names = z.namelist()
        style = z.read("its-rossellini/style.css").decode("utf-8")
    t.check(all(n.startswith("its-rossellini/") for n in names), f"{target.name}: una sola cartella its-rossellini/ ({len(names)} voci)")
    t.check("its-rossellini/readme.txt" in names and "its-rossellini/inc/requirements.php" in names, "contiene readme.txt e l'avviso dei plugin")
    t.check(not any(n.startswith("its-rossellini/dev/") or "/.git" in n for n in names), "nessun file di sviluppo nello zip")
    t.check(Path(str(target) + ".sha256").exists(), "accanto c'è l'impronta SHA-256")
    t.check("Requires PHP: 8.1" in style and "Requires at least: 6.9" in style, "lo style.css dichiara i requisiti")
