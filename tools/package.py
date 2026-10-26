"""NEOcms: installierbares ZIP bauen — für den Contao Manager („Pakete hochladen")
oder ein Composer-„artifact"-Repository.

Composer verlangt in solchen ZIPs eine Versionsnummer in der composer.json (im
Repository steht keine: dort kommt die Version aus dem Git-Tag). Das Skript
setzt die Version aus package.json ein und packt nur, was eine Installation
braucht — ohne Quellen-Build, Tests und Werkzeuge.

Vorher bauen: npm run build   (oder gleich: npm run package)
Aufruf:       python tools/package.py   → dist/neo-neocms-bundle-<version>.zip
"""
import json
import pathlib
import zipfile

ROOT = pathlib.Path(__file__).resolve().parents[1]
INCLUDE = ['src', 'config', 'contao', 'public', 'docs', 'README.md', 'CHANGELOG.md', 'LICENSE', 'THIRD-PARTY.md']


def main():
    version = json.loads((ROOT / 'package.json').read_text(encoding='utf-8'))['version']
    composer = json.loads((ROOT / 'composer.json').read_text(encoding='utf-8'))
    composer['version'] = version

    manifest = json.loads((ROOT / 'public' / 'manifest.json').read_text(encoding='utf-8'))
    for path in manifest.values():
        if not (ROOT / 'public' / path.split('/')[-1]).is_file():
            raise SystemExit(f'manifest.json verweist auf eine fehlende Datei: {path} — erst „npm run build"')

    out = ROOT / 'dist' / f'neo-neocms-bundle-{version}.zip'
    out.parent.mkdir(exist_ok=True)

    with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED) as zf:
        zf.writestr('composer.json', json.dumps(composer, indent=4, ensure_ascii=False) + '\n')
        for entry in INCLUDE:
            path = ROOT / entry
            files = [path] if path.is_file() else sorted(p for p in path.rglob('*') if p.is_file())
            for file in files:
                zf.write(file, file.relative_to(ROOT).as_posix())

    print(f'{out.relative_to(ROOT)} ({out.stat().st_size // 1024} KB)')


if __name__ == '__main__':
    main()
