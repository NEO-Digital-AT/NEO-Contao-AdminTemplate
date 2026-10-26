// Baut die ausgelieferten Dateien nach public/:
//   backend.<hash>.css  ← assets/scss/backend.scss (Dart-Sass, komprimiert)
//   backend.<hash>.js   ← assets/js/backend.js (esbuild, minifiziert)
//   manifest.json       ← Zuordnung „backend.css" → Datei mit Hash
// Der Hash im Dateinamen sorgt dafür, dass Browser und Server-Caches ein
// Update sofort sehen. Die Versionsnummer (package.json) landet zusätzlich in
// assets/scss/_build.scss und hängt als ?v= an Icon- und Schrift-Adressen.
// Aufruf: npm run build
import * as sass from 'sass';
import { transform } from 'esbuild';
import { createHash } from 'node:crypto';
import fs from 'node:fs';
import path from 'node:path';

const root = path.resolve(import.meta.dirname, '..');
const pub = path.join(root, 'public');
const { version } = JSON.parse(fs.readFileSync(path.join(root, 'package.json'), 'utf8'));

fs.writeFileSync(
  path.join(root, 'assets/scss/_build.scss'),
  `// Von tools/build.mjs geschrieben — Version aus package.json.\n// Hängt als ?v= an Icon- und Schrift-Adressen (Cache-Bust je Fassung).\n$version: '${version}';\n`,
);

const css = sass.compile(path.join(root, 'assets/scss/backend.scss'), { style: 'compressed' }).css;
const { code: js } = await transform(fs.readFileSync(path.join(root, 'assets/js/backend.js'), 'utf8'), {
  loader: 'js',
  minify: true,
  target: 'es2020',
});

// Frühere Fassungen entfernen, damit nur die aktuelle im Paket liegt
for (const file of fs.readdirSync(pub)) {
  if (/^backend\.[0-9a-f]{8}\.(css|js)$/.test(file)) {
    fs.unlinkSync(path.join(pub, file));
  }
}

const write = (name, ext, content) => {
  const hash = createHash('sha256').update(content).digest('hex').slice(0, 8);
  const file = `${name}.${hash}.${ext}`;
  fs.writeFileSync(path.join(pub, file), content);
  return `/bundles/contaoneocms/${file}`;
};

const manifest = {
  'backend.css': write('backend', 'css', css),
  'backend.js': write('backend', 'js', js),
};

fs.writeFileSync(path.join(pub, 'manifest.json'), `${JSON.stringify(manifest, null, 2)}\n`);
console.log(`NEOcms ${version}:`, manifest);
