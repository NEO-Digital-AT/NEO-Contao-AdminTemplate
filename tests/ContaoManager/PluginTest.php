<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle\Tests\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Neo\ContaoNeocmsBundle\ContaoManager\Plugin;
use Neo\ContaoNeocmsBundle\ContaoNeocmsBundle;
use PHPUnit\Framework\TestCase;

final class PluginTest extends TestCase
{
    public function testLoadsAfterTheCoreBundle(): void
    {
        $bundles = (new Plugin())->getBundles($this->createStub(ParserInterface::class));

        $this->assertCount(1, $bundles);
        $this->assertInstanceOf(BundleConfig::class, $bundles[0]);
        $this->assertSame(ContaoNeocmsBundle::class, $bundles[0]->getName());
        $this->assertSame([ContaoCoreBundle::class], $bundles[0]->getLoadAfter());
    }
}
