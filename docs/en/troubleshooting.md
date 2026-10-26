# Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| Back end still looks like Contao | Public files not installed or cache old | `php bin/console assets:install public --relative` and `php bin/console cache:clear`; check `public/bundles/contaoneocms/` exists |
| Icons are empty squares | Icon files not reachable (404) | Open `/bundles/contaoneocms/icons/fluent/edit.svg?v=…` in the browser; check web server rules for `/bundles/` |
| Some icons look grey/old | Contao icon not in the mapping | Add it to `assets/scss/backend/_icon-map.scss`, see [development.md](development.md) |
| "Vorschau" stays on the admin domain | No domain name on the website root | Site structure → website root → domain name + HTTPS |
| "Vorschau" ends in a 404 on the main domain | Web server blocks `/<prefix>/login-link` there | Allow the two addresses, see [installation.md](installation.md) |
| Local website shows "No root page found" | Domain name on the root page set locally | Leave the field empty locally |
| Shared preview link opens without preview | Link was created before the domain was entered (old link on the admin domain) | Create a new preview link |
