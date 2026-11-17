#!/usr/bin/env python3
"""Export the local WordPress as a static site, for a demo on a static host (AWS Amplify, S3, ...).

Starts from the home pages (IT and EN) of the running dev server and follows every same-origin
link in HTML, CSS and JS: pages are saved as <path>/index.html, assets as they are. Links to
http://localhost:PORT become root-relative, so the result works on any domain served at its root.
Adds 404.html, keeps <meta name="robots" content="noindex"> (it is a copy of the live site),
drops the <link> tags to the REST API and XML-RPC, which a static host does not have, then zips.

    dev/serve.sh                          # in another terminal
    python3 -I dev/export-static.py       # -> .wp/static/ and .wp/static.zip

What needs PHP does not work on a static host: site search and the document filters
(?q=, ?tipo=, ?biennio=) show the unfiltered page; the course filter works (main.js).
"""

import argparse
import html
import re
import shutil
import sys
import urllib.error
import urllib.request
import zipfile
from collections import deque
from pathlib import Path, PurePosixPath
from urllib.parse import quote, unquote, urljoin, urlsplit

ROOT = Path(__file__).resolve().parent.parent
MARKER = '.export-static'
SKIP = re.compile(
	r'^/(wp-admin|wp-login\.php|wp-json|xmlrpc\.php|wp-cron\.php|wp-comments-post\.php|wp-sitemap)'
	r'|/(feed|embed|trackback)/?$|\.php$'
)
TEXT = ('text/html', 'text/css', 'javascript', 'json', 'xml', 'text/plain')
ATTR = re.compile(r'''\b(?:href|src|poster|data-src)\s*=\s*["']([^"']+)["']''', re.I)
SRCSET = re.compile(r'''\bsrcset\s*=\s*["']([^"']+)["']''', re.I)
CSS_URL = re.compile(r'''url\(\s*["']?([^"')]+?)["']?\s*\)''', re.I)
API_LINK = re.compile(r'''<link\b[^>]*\bhref=["'][^"']*(?:/wp-json/|xmlrpc\.php)[^"']*["'][^>]*>\s*''', re.I)
ROBOTS = re.compile(r'''<meta\s+name=["']robots["']''', re.I)


def main():
	parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
	parser.add_argument('--base', default='http://localhost:8080', help='dev server address (default: %(default)s)')
	parser.add_argument('--out', default=str(ROOT / '.wp' / 'static'), help='output folder (default: .wp/static)')
	parser.add_argument('--zip', default=str(ROOT / '.wp' / 'static.zip'), help='zip to create (default: .wp/static.zip)')
	args = parser.parse_args()

	base = args.base.rstrip('/')
	origin = urlsplit(base)
	netloc = origin.netloc
	out = Path(args.out).resolve()
	absolute = re.compile(r'(?:https?:)?//' + re.escape(netloc) + r'(/[^\s"\'<>()]*)?')

	if out.exists() and any(out.iterdir()) and not (out / MARKER).exists():
		sys.exit(f'{out} non è vuota e non è un export precedente: scegli un\'altra cartella con --out.')
	shutil.rmtree(out, ignore_errors=True)
	out.mkdir(parents=True)
	(out / MARKER).touch()

	def fetch(path):
		req = urllib.request.Request(base + path, headers={'User-Agent': 'its-export-static', 'Accept-Language': 'it'})
		try:
			with urllib.request.urlopen(req, timeout=60) as res:
				return res.status, res.geturl(), res.headers.get('Content-Type', ''), res.read()
		except urllib.error.HTTPError as err:
			return err.code, err.geturl(), err.headers.get('Content-Type', ''), err.read()

	def local_path(url, ref):
		"""Same-origin path of a link (no query, no fragment), or None."""
		url = html.unescape(url.strip()).replace('\\/', '/')
		if not url or url.startswith(('data:', 'mailto:', 'tel:', 'javascript:', '#')):
			return None
		parts = urlsplit(urljoin(ref, url))
		if parts.scheme not in ('http', 'https') or parts.netloc != netloc:
			return None
		path = parts.path or '/'
		if SKIP.search(path) or '..' in path.split('/'):
			return None
		return path

	def links(text, ref, kind):
		found = set()
		flat = text.replace('\\/', '/')
		for m in absolute.finditer(flat):
			found.add(m.group(1) or '/')
		if kind == 'text/html':
			found.update(m.group(1) for m in ATTR.finditer(text))
			for m in SRCSET.finditer(text):
				found.update(c.strip().split(' ')[0] for c in m.group(1).split(','))
		if kind in ('text/html', 'text/css'):
			found.update(m.group(1) for m in CSS_URL.finditer(text))
		return {p for p in (local_path(u, ref) for u in found) if p}

	def target(path, is_page):
		rel = PurePosixPath(unquote(path).lstrip('/'))
		if is_page and (path.endswith('/') or not rel.suffix):
			rel = rel / 'index.html'
		return out.joinpath(*rel.parts) if rel.parts else out / 'index.html'

	def rewrite(text, kind):
		text = text.replace('http:\\/\\/' + netloc, '').replace('https:\\/\\/' + netloc, '')
		text = re.sub(r'(?:https?:)?//' + re.escape(netloc) + r'(?=[/"\'\s)]|$)', '', text)
		text = re.sub(r'https?%3A%2F%2F' + re.escape(quote(netloc, safe='')), '', text, flags=re.I)  # mailto: bodies
		if kind == 'text/html':
			text = API_LINK.sub('', text)
			text = re.sub(r'''<link\b[^>]*dns-prefetch[^>]*//''' + re.escape(origin.hostname) + r'''["'][^>]*>\s*''', '', text)
			if not ROBOTS.search(text):
				text = text.replace('<head>', '<head>\n<meta name="robots" content="noindex, nofollow">', 1)
		return text

	def save(dest, body, ctype, ref):
		kind = ctype.split(';')[0].strip().lower()
		new = set()
		if any(t in kind for t in TEXT):
			text = body.decode('utf-8', errors='surrogateescape')
			new = links(text, ref, kind)
			body = rewrite(text, kind).encode('utf-8', errors='surrogateescape')
		dest.parent.mkdir(parents=True, exist_ok=True)
		dest.write_bytes(body)
		return new

	queue = deque(['/', '/en/'])
	seen = set(queue)
	pages = files = 0
	broken = []

	# 404 page first: its links (menus, footer) join the crawl.
	status, url, ctype, body = fetch('/pagina-inesistente-export-statico/')
	if status == 404 and 'text/html' in ctype:
		for path in save(out / '404.html', body, ctype, url) - seen:
			seen.add(path)
			queue.append(path)

	while queue:
		path = queue.popleft()
		status, url, ctype, body = fetch(path)
		if status != 200:
			broken.append((status, path))
			continue
		final = urlsplit(url)
		if final.netloc != netloc:
			continue
		is_page = 'text/html' in ctype
		dests = {target(final.path or '/', is_page)}
		if final.path != path and is_page:
			dests.add(target(path, is_page))  # e.g. /en -> /en/: both work on the static host
		new = set()
		for dest in dests:
			new |= save(dest, body, ctype, url)
		pages += is_page
		files += 1
		for p in sorted(new - seen):
			seen.add(p)
			queue.append(p)
		if files % 50 == 0:
			print(f'{files} file, {pages} pagine...', flush=True)
	print(f'{files} file, {pages} pagine')

	left = []
	for f in out.rglob('*'):
		if f.is_file() and f.suffix in ('.html', '.css', '.js', '.json', '.xml', '.svg') and re.search(re.escape(netloc).encode() + rb'|' + re.escape(quote(netloc, safe='')).encode(), f.read_bytes(), re.I):
			left.append(f.relative_to(out).as_posix())

	zip_path = Path(args.zip).resolve()
	zip_path.unlink(missing_ok=True)
	with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
		for f in sorted(out.rglob('*')):
			if f.is_file() and f.name != MARKER:
				zf.write(f, f.relative_to(out).as_posix())

	size = sum(f.stat().st_size for f in out.rglob('*') if f.is_file()) / 1e6
	print(f'Cartella: {out}  ({size:.1f} MB)')
	print(f'Zip:      {zip_path}  ({zip_path.stat().st_size / 1e6:.1f} MB)')
	if broken:
		print(f'Link non trovati ({len(broken)}):')
		for status, path in broken:
			print(f'  {status} {path}')
	if left:
		print(f'File con ancora "{netloc}" ({len(left)}): {", ".join(left[:10])}')


if __name__ == '__main__':
	main()
