<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Neo\ContaoNeocmsBundle\ContaoNeocmsBundle;

/**
 * Managed Edition: das Bundle lädt nach dem Contao-Kern — seine Vorlagen
 * (be_main, be_login …) setzen auf die des Kerns auf.
 */
final class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoNeocmsBundle::class)->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
