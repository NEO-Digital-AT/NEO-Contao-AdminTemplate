<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle\Tests\DependencyInjection;

use Neo\ContaoNeocmsBundle\DependencyInjection\ContaoNeocmsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ContaoNeocmsExtensionTest extends TestCase
{
    public function testPrependsTheHashedBackendFilesFromTheManifest(): void
    {
        $container = new ContainerBuilder();

        (new ContaoNeocmsExtension())->prepend($container);

        $manifest = ContaoNeocmsExtension::manifest();
        $config = $container->getExtensionConfig('contao');

        $this->assertMatchesRegularExpression('#^/bundles/contaoneocms/backend\.[0-9a-f]{8}\.css$#', $manifest['backend.css']);
        $this->assertMatchesRegularExpression('#^/bundles/contaoneocms/backend\.[0-9a-f]{8}\.js$#', $manifest['backend.js']);
        $this->assertSame([ltrim($manifest['backend.css'], '/')], $config[0]['backend']['custom_css']);
        $this->assertSame([ltrim($manifest['backend.js'], '/')], $config[0]['backend']['custom_js']);
        $this->assertFileExists(__DIR__.'/../../public/'.substr($manifest['backend.css'], \strlen('/bundles/contaoneocms/')));
    }

    public function testReturnsAnEmptyManifestIfTheFileIsMissing(): void
    {
        $this->assertSame([], ContaoNeocmsExtension::manifest(__DIR__.'/does-not-exist.json'));
    }

    public function testLoadsTheServices(): void
    {
        $container = new ContainerBuilder();

        (new ContaoNeocmsExtension())->load([], $container);

        $this->assertTrue($container->hasDefinition('Neo\ContaoNeocmsBundle\Routing\FrontendBaseUrl'));
        $this->assertTrue($container->hasDefinition('Neo\ContaoNeocmsBundle\EventListener\PreviewLinkHostListener'));
        // Ausgeschlossene Klassen führt Symfony nur noch als Markierung (container.excluded)
        $this->assertTrue($container->getDefinition('Neo\ContaoNeocmsBundle\ContaoNeocmsBundle')->hasTag('container.excluded'));
        $this->assertFalse($container->getDefinition('Neo\ContaoNeocmsBundle\Routing\FrontendBaseUrl')->hasTag('container.excluded'));
    }
}
