<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle\Routing;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\PageModel;

/**
 * Hauptdomain der Website, z. B. „https://neo-digital.at", und ihr Startpunkt.
 *
 * Das Backend läuft auf einer eigenen Subdomain (admin.…). Links ins Frontend —
 * „Zum Frontend" auf der Anmeldung, „Vorschau", die teilbaren Vorschau-Links —
 * sollen trotzdem auf die Hauptdomain zeigen. Die Domain kommt aus Contao selbst:
 * Seitenstruktur → Startpunkt → „Domainname" und „HTTPS verwenden". Bei mehreren
 * Startpunkten mit Domain gewinnt der Sprachen-Fallback.
 *
 * Ohne eingetragene Domain liefert die Klasse null — dann bleibt Contaos
 * Verhalten (Links auf die Domain, auf der man gerade ist). Lokal ist das gewollt:
 * dort läuft alles unter localhost.
 */
final class FrontendBaseUrl
{
    private PageModel|false|null $root = false;

    public function __construct(private readonly ContaoFramework $framework)
    {
    }

    public function get(): string|null
    {
        $root = $this->rootPage();

        return null === $root ? null : ($root->useSSL ? 'https' : 'http').'://'.trim((string) $root->dns);
    }

    /** Der veröffentlichte Startpunkt mit Domainname (Fallback bevorzugt). */
    public function rootPage(): PageModel|null
    {
        if (false !== $this->root) {
            return $this->root;
        }

        $this->framework->initialize();
        $chosen = null;

        foreach ($this->framework->getAdapter(PageModel::class)->findPublishedRootPages() ?? [] as $root) {
            if ('' === trim((string) $root->dns)) {
                continue;
            }

            $chosen ??= $root;

            if ($root->fallback) {
                $chosen = $root;
                break;
            }
        }

        return $this->root = $chosen;
    }
}
