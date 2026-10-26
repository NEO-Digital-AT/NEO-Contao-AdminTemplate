# Development

## Structure

| Path | Contents |
|---|---|
| `assets/scss/` | Sources of the stylesheet (`backend.scss` + `backend/_*.scss`, NEO tokens) |
| `assets/js/backend.js` | Sidebar collapse (state in `localStorage`) |
| `public/` | Shipped files: `backend.<hash>.css/js`, `manifest.json`, `icons/fluent/`, `fonts/`, `brand/` |
| `contao/templates/` | Overrides of `be_main`, `be_login`, `be_login_two_factor` + logo partial |
| `contao/languages/` | Product name (`MSC.neocms_name`) |
| `src/` | Bundle, manager plugin, extension (hooks the stylesheet into `contao.backend.custom_css`), main domain logic |
| `tools/` | `build.mjs` (CSS/JS), `fluent-icons.py` (icons), `package.py` (ZIP) |

The bundle hooks itself in via `prepend()`: it reads `public/manifest.json` and adds the
hashed files to `contao.backend.custom_css` / `custom_js` — installations keep their own
entries.

## Build

```bash
npm install          # once: sass + esbuild (pinned in package.json)
npm run build        # public/backend.<hash>.css/js + manifest.json
npm run package      # build + dist/neo-neocms-bundle-<version>.zip
composer install && vendor/bin/phpunit
```

Release: raise `version` in `package.json`, add a `CHANGELOG.md` entry, `npm run package`,
commit `public/` (built files are part of the package), tag `vX.Y.Z`.

## Icons

The mapping Contao icon → Fluent icon is in `assets/scss/backend/_icon-map.scss`. To add
an icon:

1. Enter the Fluent name (without size/style, e.g. `'edit'`; filled: `'star_filled'`).
   Names: <https://github.com/microsoft/fluentui-system-icons>.
2. Get the package once: `npm pack @fluentui/svg-icons` and unpack it.
3. `python tools/fluent-icons.py <unpacked>/package` — copies exactly the icons in use to
   `public/icons/fluent/`.
4. `npm run build`.

In the stylesheet icons are always masks via `fl()` / `fl-icon()` / `fl-img()` and
coloured by CSS. URLs are relative to the stylesheet — never put an icon URL into one of
Contao's own `--icon-*` variables (it would resolve against Contao's folder).

## Licences of bundled files

Fluent icons: `public/icons/fluent/LICENSE.txt` (MIT, written by `tools/fluent-icons.py`).
Space Grotesk: `public/fonts/OFL.txt` (SIL OFL 1.1). Satoshi: credit line in
`THIRD-PARTY.md`. Adding a font or icon set means adding its licence the same way.
