<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle\EventListener;

use Neo\ContaoNeocmsBundle\Routing\FrontendBaseUrl;
use Contao\CoreBundle\Event\ContaoCoreEvents;
use Contao\CoreBundle\Event\PreviewUrlConvertEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * „Vorschau" im Kopfmenü ohne bestimmte Seite → Startseite der Hauptdomain.
 *
 * Für eine bestimmte Seite (Seitenbaum, Artikel, Nachrichten) baut Contao die
 * Vorschau-Adresse mit der Domain des Startpunkts — liegt die auf der Hauptdomain,
 * meldet Contao den Redakteur dort per signiertem Einmal-Link an (Server: die
 * Login-Link-Adresse muss auf der Hauptdomain erreichbar sein, docs/en/installation.md). Der Knopf „Vorschau" im Kopfmenü schickt aber keine Seite
 * mit; dann springt Contao auf „/" der Domain, auf der man gerade ist — die
 * Admin-Subdomain. Hier bekommt er den Startpunkt der Hauptdomain als Seite; den
 * Rest erledigt Contaos eigener Listener (läuft danach, Priorität 0).
 * Ohne Domain am Startpunkt bleibt alles wie bei Contao.
 */
#[AsEventListener(ContaoCoreEvents::PREVIEW_URL_CONVERT, priority: 10)]
final class PreviewStartPageListener
{
    public function __construct(private readonly FrontendBaseUrl $frontendBaseUrl)
    {
    }

    public function __invoke(PreviewUrlConvertEvent $event): void
    {
        $query = $event->getRequest()->query;

        if ($query->has('url') || $query->has('article') || $query->has('page')) {
            return;
        }

        if ($root = $this->frontendBaseUrl->rootPage()) {
            $query->set('page', (string) $root->id);
        }
    }
}
