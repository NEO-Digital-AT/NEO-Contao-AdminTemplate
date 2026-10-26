<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle\Tests\Routing;

use Contao\PageModel;
use Contao\TestCase\ContaoTestCase;
use Neo\ContaoNeocmsBundle\Routing\FrontendBaseUrl;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

// Die Contao-Hilfen (mockAdapter, mockContaoFramework) liefern Mocks ohne Erwartungen.
#[AllowMockObjectsWithoutExpectations]
final class FrontendBaseUrlTest extends ContaoTestCase
{
    public function testReturnsNullWithoutADomain(): void
    {
        $service = $this->service([$this->root(['dns' => '', 'useSSL' => true, 'fallback' => true])]);

        $this->assertNull($service->get());
        $this->assertNull($service->rootPage());
    }

    public function testReturnsNullWithoutRootPages(): void
    {
        $this->assertNull($this->service(null)->get());
    }

    public function testUsesSchemeAndDomainOfTheRootPage(): void
    {
        $this->assertSame('https://neo-digital.at', $this->service([$this->root(['dns' => 'neo-digital.at', 'useSSL' => true, 'fallback' => true])])->get());
        $this->assertSame('http://kunde.test', $this->service([$this->root(['dns' => ' kunde.test ', 'useSSL' => false, 'fallback' => true])])->get());
    }

    public function testPrefersTheLanguageFallback(): void
    {
        $service = $this->service([
            $this->root(['id' => 1, 'dns' => '', 'useSSL' => true, 'fallback' => true]),
            $this->root(['id' => 2, 'dns' => 'en.kunde.at', 'useSSL' => true, 'fallback' => false]),
            $this->root(['id' => 3, 'dns' => 'kunde.at', 'useSSL' => true, 'fallback' => true]),
        ]);

        $this->assertSame('https://kunde.at', $service->get());
        $this->assertSame(3, $service->rootPage()->id);
    }

    public function testTakesTheFirstRootPageWithADomainIfNoneIsTheFallback(): void
    {
        $service = $this->service([
            $this->root(['id' => 1, 'dns' => 'a.kunde.at', 'useSSL' => true, 'fallback' => false]),
            $this->root(['id' => 2, 'dns' => 'b.kunde.at', 'useSSL' => true, 'fallback' => false]),
        ]);

        $this->assertSame('https://a.kunde.at', $service->get());
    }

    private function service(array|null $roots): FrontendBaseUrl
    {
        $adapter = $this->mockAdapter(['findPublishedRootPages']);
        $adapter->method('findPublishedRootPages')->willReturn($roots);

        return new FrontendBaseUrl($this->mockContaoFramework([PageModel::class => $adapter]));
    }

    private function root(array $properties): PageModel
    {
        return $this->mockClassWithProperties(PageModel::class, $properties + ['type' => 'root']);
    }
}
