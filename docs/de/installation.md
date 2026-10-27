# Installation

**Voraussetzungen:** Contao 5.7 (Managed Edition), PHP 8.2 oder neuer. Keine
Datenbank-Änderungen, für den Look nichts einzustellen.

## Weg A — Contao Manager

1. Contao Manager → **Pakete** → **Pakete hochladen**.
2. `neo-neocms-bundle-<version>.zip` wählen.
3. **Änderungen anwenden.** Fertig.

## Weg B — Composer aus dem Git-Repository

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/NEO-Digital-AT/NEO-Contao-AdminTemplate" }
],
"require": { "neo/neocms-bundle": "^1.0" }
```

Danach `composer update neo/neocms-bundle`. Privates Repository: GitHub-Token für
Composer hinterlegen.

## Weg C — ZIP im Projekt (z. B. Docker-Build ohne GitHub-Zugang)

ZIP in einen Projektordner legen, z. B. `artifacts/`, und eintragen:

```json
"repositories": [ { "type": "artifact", "url": "artifacts/" } ],
"require": { "neo/neocms-bundle": "^1.0" }
```

Im Docker-Build `artifacts/` **vor** `composer install/update` ins Image kopieren.

## Hauptdomain für „Zum Frontend", „Vorschau" und Vorschau-Links

Nur nötig, wenn das Backend auf einer eigenen Subdomain läuft (z. B. `admin.kunde.at`):

1. **Seitenstruktur → Startpunkt bearbeiten:** **Domainname** (`kunde.at`) eintragen,
   **HTTPS verwenden** anhaken, speichern. Ohne Eintrag bleibt alles wie bei Contao.
2. **Webserver der Hauptdomain:** Ist dort `/manage` gesperrt, genau zwei Adressen
   freigeben — `/manage/login-link` und `/manage/preview_switch` (nginx-Beispiel in
   [../en/installation.md](../en/installation.md)). Gleich mitprüfen: Zeigt
   `kunde.at/index.php/manage/login` die Backend-Anmeldung? Dann den Umweg sperren.
3. Lokal (localhost) das Feld **leer lassen**.

## Entfernen — Contao bleibt unangetastet

Das Paket überschreibt **keine** Contao-Datei: Contaos Originale bleiben in
`vendor/contao/…`, die Vorlagen des Pakets setzen per Vorlagen-Vererbung darauf auf,
Stylesheet und Skript kommen über Contaos eigene Einstellung `custom_css`/`custom_js`.
Keine Datenbank-Tabellen, keine Felder, keine Migrationen.

- Contao Manager: Pakete → `neo/neocms-bundle` → **Entfernen** → Änderungen anwenden.
- Composer: `composer remove neo/neocms-bundle` (bei „artifact" auch das ZIP löschen).

Danach sieht das Backend wieder aus wie Standard-Contao. Domainname am Startpunkt und
Server-Regeln dürfen bleiben — sie gehören zu Contao bzw. zum Server, nicht zum Paket.

## Prüfen

Anmeldung zeigt das NEOcms-Logo, Browser-Tab „NEOcms by Contao | …"; Profil-Menü →
„Dunkles Design" schaltet auf Neongrün.
