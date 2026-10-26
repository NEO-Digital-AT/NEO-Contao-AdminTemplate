# Upgrading

## Updating the bundle

- Contao Manager: upload the new ZIP and apply — or `composer update neo/neocms-bundle`.
- File names of the stylesheet and script carry a hash; browsers and server caches pick
  up the new version immediately. Nothing to clear by hand.
- What changes per version and what to do: [CHANGELOG.md](../../CHANGELOG.md).

## After a Contao update (check list)

The bundle extends Contao without modifying it. Three places depend on Contao's markup:

1. `contao/templates/be_main.html.twig` overrides the `header` block — compare with
   `vendor/contao/core-bundle/contao/templates/twig/be_main.html.twig`; only the collapse
   button and the content of `#home` may differ.
2. `contao/templates/be_login.html.twig` replaces the `<title>` and the "Zum Frontend"
   link by text replacement. If Contao changes that markup, the replacement silently
   stops working and Contao's original shows — nothing breaks.
3. `src/EventListener/PreviewLinkHostListener.php` replaces two callbacks of Contao's
   internal `PreviewLinkListener` — compare with
   `vendor/contao/core-bundle/src/EventListener/DataContainer/PreviewLinkListener.php`.

Then open the back end in light and dark mode: login, article list, site structure,
files, edit an element, open a dropdown, collapse/expand the sidebar.

If Contao renames a back end variable or icon, the affected detail falls back to Contao's
look — nothing breaks.
