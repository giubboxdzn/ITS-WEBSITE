#!/usr/bin/env python3
"""Theme translations, without wp-cli or gettext tools.

1. Extracts the theme's gettext strings (text domain its-rossellini) into languages/its-rossellini.pot.
2. Merges them into every languages/<locale>.po (new strings get an empty msgstr, gone ones are dropped).
3. Compiles each .po into <locale>.mo and <locale>.l10n.php (the PHP format WordPress 6.5+ loads first).

Usage: python3 -I dev/i18n/build.py
"""
import os
import re
import struct
import sys

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
THEME = os.path.join(ROOT, "wp-content", "themes", "its-rossellini")
LANG_DIR = os.path.join(THEME, "languages")
DOMAIN = "its-rossellini"
STR = r"'((?:[^'\\]|\\.)*)'"
CALLS = [
    (re.compile(rf"\b(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*{STR}\s*,\s*'{DOMAIN}'\s*\)"), "single"),
    (re.compile(rf"\b(?:_x|esc_html_x|esc_attr_x|_ex)\(\s*{STR}\s*,\s*{STR}\s*,\s*'{DOMAIN}'\s*\)"), "context"),
    (re.compile(rf"\b_n\(\s*{STR}\s*,\s*{STR}\s*,[^;]*?'{DOMAIN}'\s*\)"), "plural"),
]


def unescape(s):
    return s.replace("\\'", "'").replace("\\\\", "\\")


def extract():
    entries = {}
    for folder, _, files in os.walk(THEME):
        if os.sep + "languages" in folder:
            continue
        for name in sorted(files):
            if not name.endswith(".php"):
                continue
            path = os.path.join(folder, name)
            rel = os.path.relpath(path, THEME).replace(os.sep, "/")
            with open(path, encoding="utf-8") as f:
                text = f.read()
            for pattern, kind in CALLS:
                for m in pattern.finditer(text):
                    line = text.count("\n", 0, m.start()) + 1
                    if kind == "single":
                        key = (None, unescape(m.group(1)), None)
                    elif kind == "context":
                        key = (unescape(m.group(2)), unescape(m.group(1)), None)
                    else:
                        key = (None, unescape(m.group(1)), unescape(m.group(2)))
                    entries.setdefault(key, []).append(f"{rel}:{line}")
    return entries


def po_quote(s):
    return '"' + s.replace("\\", "\\\\").replace('"', '\\"').replace("\n", "\\n") + '"'


def po_unquote(s):
    s = s.strip()[1:-1]
    return re.sub(r'\\(.)', lambda m: {"n": "\n", "t": "\t"}.get(m.group(1), m.group(1)), s)


def read_po(path):
    """{(ctx, msgid, plural): [msgstr, ...]} and the header block."""
    out, header = {}, ""
    if not os.path.exists(path):
        return out, header
    with open(path, encoding="utf-8") as f:
        blocks = f.read().split("\n\n")
    for block in blocks:
        fields, current = {}, None
        for line in block.splitlines():
            if line.startswith("#") or not line.strip():
                continue
            m = re.match(r'(msgctxt|msgid_plural|msgid|msgstr(?:\[\d\])?)\s+(".*")$', line)
            if m:
                current = m.group(1)
                fields[current] = po_unquote(m.group(2))
            elif line.startswith('"') and current:
                fields[current] += po_unquote(line)
        if "msgid" not in fields:
            continue
        if fields["msgid"] == "":
            header = fields.get("msgstr", "")
            continue
        key = (fields.get("msgctxt"), fields["msgid"], fields.get("msgid_plural"))
        if key[2] is not None:
            out[key] = [fields.get(f"msgstr[{i}]", "") for i in range(2)]
        else:
            out[key] = [fields.get("msgstr", "")]
    return out, header


def write_po(path, entries, translations, header):
    lines = ['msgid ""', "msgstr " + po_quote(header), ""]
    for key in sorted(entries, key=lambda k: entries[k][0]):
        ctx, msgid, plural = key
        lines.append("#: " + " ".join(entries[key]))
        if ctx is not None:
            lines.append("msgctxt " + po_quote(ctx))
        lines.append("msgid " + po_quote(msgid))
        tr = translations.get(key, [""] * (2 if plural is not None else 1))
        if plural is not None:
            lines.append("msgid_plural " + po_quote(plural))
            for i, t in enumerate((tr + ["", ""])[:2]):
                lines.append(f"msgstr[{i}] " + po_quote(t))
        else:
            lines.append("msgstr " + po_quote(tr[0]))
        lines.append("")
    with open(path, "w", encoding="utf-8", newline="\n") as f:
        f.write("\n".join(lines))


def compile_mo(path, translations, header):
    items = {"": header}
    for (ctx, msgid, plural), tr in translations.items():
        if not any(tr):
            continue
        key = (ctx + "\x04" if ctx else "") + msgid + ("\x00" + plural if plural is not None else "")
        items[key] = "\x00".join(tr)
    keys = sorted(items)
    ids = b"".join(k.encode("utf-8") + b"\x00" for k in keys)
    strs = b"".join(items[k].encode("utf-8") + b"\x00" for k in keys)
    n = len(keys)
    key_start = 7 * 4 + 16 * n
    value_start = key_start + len(ids)
    offsets, k_off, v_off = [], 0, 0
    for k in keys:
        kb, vb = k.encode("utf-8"), items[k].encode("utf-8")
        offsets.append((len(kb), key_start + k_off, len(vb), value_start + v_off))
        k_off += len(kb) + 1
        v_off += len(vb) + 1
    out = struct.pack("Iiiiiii", 0x950412DE, 0, n, 7 * 4, 7 * 4 + 8 * n, 0, 0)
    out += b"".join(struct.pack("ii", ln, off) for ln, off, _, _ in offsets)
    out += b"".join(struct.pack("ii", ln, off) for _, _, ln, off in offsets)
    with open(path, "wb") as f:
        f.write(out + ids + strs)


def php_quote(s):
    return "'" + s.replace("\\", "\\\\").replace("'", "\\'") + "'"


def compile_php(path, translations, locale):
    lines = ["<?php", "// Generated by dev/i18n/build.py from " + locale + ".po.", "return array(",
             "\t'domain' => 'its-rossellini',", f"\t'language' => '{locale}',",
             "\t'plural-forms' => 'nplurals=2; plural=(n != 1);',", "\t'messages' => array("]
    for (ctx, msgid, plural), tr in sorted(translations.items(), key=lambda kv: kv[0][1]):
        if not any(tr):
            continue
        key = (ctx + "\x04" if ctx else "") + msgid
        value = "\x00".join(tr)
        lines.append("\t\t" + php_quote(key).replace("\x04", "' . \"\\4\" . '") + " => " + php_quote(value).replace("\x00", "' . \"\\0\" . '") + ",")
    lines += ["\t),", ");", ""]
    with open(path, "w", encoding="utf-8", newline="\n") as f:
        f.write("\n".join(lines))


def main():
    os.makedirs(LANG_DIR, exist_ok=True)
    entries = extract()
    pot_header = "Project-Id-Version: ITS Rossellini\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit\nX-Domain: its-rossellini\n"
    write_po(os.path.join(LANG_DIR, "its-rossellini.pot"), entries, {}, pot_header)
    for name in sorted(os.listdir(LANG_DIR)):
        if not name.endswith(".po"):
            continue
        locale = name[:-3]
        path = os.path.join(LANG_DIR, name)
        translations, header = read_po(path)
        header = header or pot_header + f"Language: {locale}\nPlural-Forms: nplurals=2; plural=(n != 1);\n"
        kept = {k: v for k, v in translations.items() if k in entries}
        write_po(path, entries, kept, header)
        compile_mo(os.path.join(LANG_DIR, locale + ".mo"), kept, header)
        compile_php(os.path.join(LANG_DIR, locale + ".l10n.php"), kept, locale)
        done = sum(1 for v in kept.values() if all(v))
        print(f"{locale}: {done}/{len(entries)} translated")
    print(f"pot: {len(entries)} strings")


if __name__ == "__main__":
    sys.exit(main())
