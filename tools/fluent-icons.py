"""NEOcms: Fluent-Icons (Microsoft Fluent UI System Icons, MIT) fürs Contao-Backend exportieren.

Liest, welche Icons das Backend-Theme braucht — alle Werte in
assets/scss/backend/_icon-map.scss und alle fl('…')-, fl-icon('…')- und fl-img('…')-
Aufrufe in assets/scss/backend/ —
und kopiert genau diese 20-px-Varianten nach public/icons/fluent/.
Contao liefert sie unter /bundles/contaoneocms/icons/fluent/ aus; das CSS nutzt
sie als Maske und färbt sie selbst (hell violett, dunkel neon).

Paket holen (einmalig, beliebiger Ordner):
    npm pack @fluentui/svg-icons && tar -xzf fluentui-svg-icons-*.tgz
Aufruf (im Bundle-Ordner):
    python tools/fluent-icons.py <pfad>/package
"""
import pathlib
import re
import shutil
import sys

ROOT = pathlib.Path(__file__).resolve().parents[1]
SCSS = ROOT / 'assets' / 'scss' / 'backend'
OUT = ROOT / 'public' / 'icons' / 'fluent'

# Das npm-Paket bringt keine LICENSE-Datei mit (nur "license": "MIT" in package.json).
LICENSE = """Fluent UI System Icons (@fluentui/svg-icons) – https://github.com/microsoft/fluentui-system-icons

MIT License

Copyright (c) 2020 Microsoft Corporation

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
"""


def wanted_names():
    names = set()
    for f in SCSS.glob('*.scss'):
        text = f.read_text(encoding='utf-8')
        if f.name == '_icon-map.scss':
            names.update(re.findall(r"^\s*'[^']+':\s*'([a-z0-9_]+)',", text, re.M))
        names.update(re.findall(r"\bfl(?:-icon|-img)?\(\s*'([a-z0-9_]+)'", text))
    return sorted(names)


def source_file(icons, name):
    style = 'filled' if name.endswith('_filled') else 'regular'
    base = name[:-len('_filled')] if style == 'filled' else name
    return icons / f'{base}_20_{style}.svg'


def main():
    if len(sys.argv) != 2:
        sys.exit(__doc__)
    package = pathlib.Path(sys.argv[1])
    icons = package / 'icons'
    names = wanted_names()
    missing = [n for n in names if not source_file(icons, n).exists()]
    if missing:
        sys.exit('Im Fluent-Paket nicht gefunden: ' + ', '.join(missing))

    if OUT.exists():
        shutil.rmtree(OUT)
    OUT.mkdir(parents=True)
    for name in names:
        svg = source_file(icons, name).read_text(encoding='utf-8')
        # Breite/Höhe raus: Größe bestimmt das CSS, die viewBox bleibt.
        svg = re.sub(r'\s(width|height)="\d+"', '', svg, count=2)
        (OUT / f'{name}.svg').write_text(svg, encoding='utf-8')
    (OUT / 'LICENSE.txt').write_text(LICENSE, encoding='utf-8')
    print(f'{len(names)} Icons nach {OUT.relative_to(ROOT)}')


if __name__ == '__main__':
    main()
