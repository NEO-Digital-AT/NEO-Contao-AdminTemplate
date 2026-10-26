# Using NEOcms

Everything works as in Contao — only the look is different.

| Area | What you see | How to use it |
|---|---|---|
| Light / dark | Violet (light) or neon green (dark) | Profile menu (top right) → "Dark design" / "Light design". Without a choice the back end follows the computer's setting. |
| Header | NEOcms logo, button ☰ on the left | ☰ collapses the sidebar to an icon rail; the browser remembers it. On tablets and phones Contao's own menu applies. |
| Sidebar | Areas ("Content", "Layout" …) with a chevron | Click an area heading to collapse/expand it; Contao remembers it per user. In the icon rail the module name shows as a tooltip. |
| Content | A card on a grey canvas; toolbar with icon + text | Unchanged Contao functions ("New", "Expand all", "Edit multiple" …). |
| Fields | 40 px, line underneath turns into the accent colour on focus | Unchanged. Yes/no fields are switches (space bar toggles). Selection checkboxes in lists stay checkboxes. |
| Dropdowns | Panel with a fine line, tick at the selected entry | Unchanged; search field for long lists. |
| Messages | Error/success/hint in NEO colours with an icon | Unchanged. |
| "Zum Frontend", "Vorschau" | Open the website on its main domain | See [installation.md](installation.md) → main domain. |

Screenshots: [`docs/screenshots/`](../screenshots/) (light and dark).

## Accessibility

Colour pairs meet WCAG 2.1 AA (text ≥ 4.5:1, field underline, switch track and focus
≥ 3:1). Replaced icons keep their alt text; the sidebar button reports its state
(`aria-expanded`); in the icon rail the module name stays available to screen readers.
Animations stop with "reduce motion".
