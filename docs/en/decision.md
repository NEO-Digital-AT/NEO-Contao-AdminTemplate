# Decision record: a bundle of its own

**Date:** 2026-10-26 · **Decided by:** project owner (NEO Digital) · **Status:** accepted

**Need:** the NEO back end look ("NEOcms by Contao") on all client websites, installable
like any Contao extension, updatable in one place.

| Step | Checked | Result |
|---|---|---|
| 1. Contao core | `contao.backend.custom_css` / `custom_js`, template overrides | Core can load a stylesheet, but has no theming of its own → used as the mechanism inside the bundle |
| 2. Third-party extension | Contao extension list | Back end themes exist, but none carries the NEO design (logo, colours, Fluent icons, Roundcube look) |
| 3. Existing NEO extension | super-agent, brevo-newsletter, llms bundles | None covers the back end |
| 4. Extend an existing one | — | Different purpose; mixing would violate "one extension per task" |
| 5. New extension | — | **`neo/neocms-bundle`**, repository `Contao-NEOcms-by-NEO` |

**Consequences:** one source for all installations (the neo-digital.at website uses the
package too); updates via Composer / Contao Manager; Contao core stays untouched.
