<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Lädt die Dienste und hängt Stylesheet und Skript an jedes Backend —
 * über Contaos eigene Einstellung contao.backend.custom_css / custom_js.
 *
 * Die Dateinamen tragen einen Hash (backend.1a2b3c4d.css, aus
 * public/manifest.json): Server wie nginx halten /bundles/ oft ein Jahr im
 * Cache; ein Update bekommt so automatisch neue Adressen. Eigene
 * custom_css-Einträge der Installation bleiben erhalten (Contao führt die
 * Listen zusammen).
 */
final class ContaoNeocmsExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        (new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config')))->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $manifest = self::manifest();
        $backend = [];

        if (isset($manifest['backend.css'])) {
            $backend['custom_css'] = [ltrim($manifest['backend.css'], '/')];
        }

        if (isset($manifest['backend.js'])) {
            $backend['custom_js'] = [ltrim($manifest['backend.js'], '/')];
        }

        if ([] !== $backend) {
            $container->prependExtensionConfig('contao', ['backend' => $backend]);
        }
    }

    /**
     * @return array<string, string>
     */
    public static function manifest(string|null $path = null): array
    {
        $path ??= __DIR__.'/../../public/manifest.json';

        if (!is_file($path)) {
            return [];
        }

        $data = json_decode((string) file_get_contents($path), true);

        return \is_array($data) ? $data : [];
    }
}
