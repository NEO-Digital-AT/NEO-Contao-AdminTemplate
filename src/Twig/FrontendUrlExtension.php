<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle\Twig;

use Neo\ContaoNeocmsBundle\Routing\FrontendBaseUrl;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig-Funktion neocms_frontend_url(): Startseite der Hauptdomain, z. B. für den Link
 * „Zum Frontend" auf der Backend-Anmeldung (contao/templates/be_login.html.twig).
 * Ohne Domain am Startpunkt „/" — wie Contao es von sich aus verlinkt.
 */
final class FrontendUrlExtension extends AbstractExtension
{
    public function __construct(private readonly FrontendBaseUrl $frontendBaseUrl)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('neocms_frontend_url', fn (): string => ($this->frontendBaseUrl->get() ?? '').'/'),
        ];
    }
}
