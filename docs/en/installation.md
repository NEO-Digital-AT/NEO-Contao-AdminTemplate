# Installation

## Requirements

- Contao **5.7** Managed Edition, PHP **8.2** or newer.
- No database changes, no configuration needed for the look.

## Option A — Contao Manager (upload)

1. Contao Manager → **Packages** → **Upload packages**.
2. Choose `neo-neocms-bundle-<version>.zip` (built with `npm run package`, see
   [development.md](development.md)).
3. Click **Apply changes**. The Contao Manager installs the package and clears the cache.

## Option B — Composer from the Git repository

In the website's `composer.json`:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/NEO-Digital-AT/Contao-NEOcms-by-NEO" }
],
"require": {
    "neo/neocms-bundle": "^1.0"
}
```

Then `composer update neo/neocms-bundle`. For a private repository Composer needs a
GitHub token (`composer config --global github-oauth.github.com <token>`).

## Option C — Composer from a ZIP inside the project

For builds without network access to GitHub (e.g. Docker builds): put the ZIP in a
folder of the project and register it as an artifact repository:

```json
"repositories": [
    { "type": "artifact", "url": "artifacts/" }
],
"require": {
    "neo/neocms-bundle": "^1.0"
}
```

In a Docker build, copy `artifacts/` into the image **before** `composer install/update`.

After A, B or C: Contao installs the public files to `public/bundles/contaoneocms/`
(`assets:install`, runs automatically with the Contao Manager and `contao-setup`).

## Main domain for front end links (back end on its own sub domain)

If the back end runs on e.g. `admin.example.at` and the website on `example.at`:

1. **Site structure → website root → edit:** enter the **domain name** (`example.at`) and
   tick **Use HTTPS**. Without this entry everything behaves like Contao (links on the
   current domain).
2. **Web server of the main domain:** if `/manage` (the back end route prefix) is blocked
   there, allow exactly these two addresses — Contao uses them for the preview:
   - `/<prefix>/login-link` — signed one-time link that signs the editor in for the preview
   - `/<prefix>/preview_switch` — the switches of the preview bar

   nginx example (route prefix `/manage`):

   ```nginx
   location ^~ /manage { return 404; }
   # also block the detour through the front controller
   location ^~ /index.php/manage { return 404; }
   location = /manage/login-link     { try_files $uri /index.php$is_args$args; }
   location = /manage/preview_switch { try_files $uri /index.php$is_args$args; }
   ```

   ⚠️ Check whether `/index.php/<prefix>/login` shows the back end login on the main
   domain. If it does, the back end is reachable there despite the block — the second
   `location` above closes that gap.
3. After a preview the editor is signed in on the main domain too, but can only use the
   preview there.

Locally (e.g. `localhost`) leave the domain field **empty** — otherwise the local site
finds no page.

## Uninstalling — Contao stays untouched

The package overwrites **no** Contao file: Contao's originals stay in
`vendor/contao/…`, the package's templates extend them via template inheritance, and
stylesheet/script are added through Contao's own `custom_css`/`custom_js` setting. No
database tables, fields or migrations.

- Contao Manager: Packages → `neo/neocms-bundle` → **Remove** → Apply changes.
- Composer: `composer remove neo/neocms-bundle` (with an artifact repository also delete
  the ZIP).

The back end then looks like standard Contao again. The domain name on the website root
and web server rules can stay — they are Contao/server settings, not part of the package.

## Checking the installation

- Back end login shows the NEOcms logo; browser tab "NEOcms by Contao | …".
- In the HTML of a back end page: `<link rel="stylesheet" href="/bundles/contaoneocms/backend.<hash>.css">`.
- Profile menu → "Dark design": logo and accent colour switch to neon green.
