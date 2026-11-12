# Changelog

All notable changes to `neo/neocms-bundle`. Versioning: [Semantic Versioning](https://semver.org).
Written for whoever updates an installation: what changes for you, what to do.

## 1.0.3 — 2026-11-12

- **Toolbar above lists:** every button has the same shape now — also the ones extensions
  add with their own icon (e.g. Skills, Protokoll, Verbindungen): same height, padding and
  hover area as "New" or "Edit multiple". Before, they kept Contao's tight base style.
- **Right edge:** Contao pulls its empty "more" menu 15 px to the left; together with the
  toolbar gaps this pushed the last button over the right edge of the card. It ends inside
  the padding now.
- **One icon per button:** where NEOcms draws the icon (back, new, edit multiple …), an image
  an extension passes along (e.g. a back arrow) is hidden — before, two arrows stood side by side.
- Nothing to do: install the new package; Contao clears its cache on update.

## 1.0.2 — 2026-11-12

- **Menu icons for the NEO extensions:** Access keys and licences (keys), Website-Check
  (gauge), Cookie notice (cookie), Frontend Editor (window with pen), Translations,
  Page styles (palette); the website's calculators in System (checklist, calculator,
  inbox). Before, these entries showed the fallback icon. Five new Fluent icons in
  `public/icons/fluent/`.
- **Save button while sending:** Contao disables the button while the form is being sent;
  its disabled colours (forced with `!important`) made the label unreadable — pale violet
  with white text, in dark mode dark grey with dark text. The main action now keeps the
  pressed colour (dark violet with white text, dark mode deep neon green with dark text)
  until the page reloads.
- **Preview icon on phones:** below 768 px Contao draws its own monitor icon as background
  image again; it lay under the NEOcms icon. Only the NEOcms icon is shown now, centred.
- Nothing to do: install the new package; Contao clears its cache on update.

## 1.0.1 — 2026-10-27

- **Licence:** all rights reserved by Erich Nigg (NEO Digital). Use only by the rights
  holder; no right for anyone else to use, copy, modify, publish or sell the package.
  German text binding, English translation included (`LICENSE`).
- No code changes — nothing to do for existing installations.

## 1.0.0 — 2026-10-26

First release.

- **Back end look "NEOcms by Contao"**, modelled on the NEO webmail skin for Roundcube:
  48 px header with the NEOcms logo, content as a card on a grey canvas, light (violet)
  and dark (neon green) scheme, Satoshi / Space Grotesk.
- **Navigation:** Fluent icon per module, areas collapse/expand (Contao core feature,
  now with a visible chevron), the whole sidebar collapses to an icon rail (button in
  the header, remembered per browser).
- **Microsoft Fluent UI System Icons** replace Contao's icons (images and background
  icons); unmapped icons are shown greyscale.
- **Fields** in Fluent style (40 px, 4 px radius, accent underline on focus), switches
  instead of form checkboxes, Fluent dropdowns, no shadows/glow.
- **Login page** with the stacked NEOcms logo, centred.
- **Front end links on the main domain** when the back end runs on its own sub domain:
  "Zum Frontend", "Vorschau" (header menu) and shareable preview links use the domain
  of the website root (site structure → website root → domain name + HTTPS).
  Requires two server addresses on the main domain, see `docs/en/installation.md`.

**To do after installing:** enter the domain name on the website root if the back end
runs on its own sub domain (see installation guide). Nothing else.
