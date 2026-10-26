<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * NEOcms — das Contao-Backend im NEO-Look (Kopfzeile, Seitenleiste, Fluent-Icons,
 * Felder, Anmeldung) plus Frontend-Links auf die Hauptdomain.
 * Öffentliche Dateien liegen in public/ → /bundles/contaoneocms/.
 */
final class ContaoNeocmsBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
