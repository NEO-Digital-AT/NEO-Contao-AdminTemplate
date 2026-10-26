<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle\Tests\EventListener;

use Contao\CoreBundle\Event\PreviewUrlConvertEvent;
use Contao\PageModel;
use Contao\TestCase\ContaoTestCase;
use Neo\ContaoNeocmsBundle\EventListener\PreviewStartPageListener;
use Neo\ContaoNeocmsBundle\Routing\FrontendBaseUrl;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;

// Die Contao-Hilfen (mockAdapter, mockContaoFramework) liefern Mocks ohne Erwartungen.
#[AllowMockObjectsWithoutExpectations]
final class PreviewStartPageListenerTest extends ContaoTestCase
{
    public function testSetsTheRootPageIfThePreviewHasNoTarget(): void
    {
        $request = new Request();

        ($this->listener('neo-digital.at'))(new PreviewUrlConvertEvent($request));

        $this->assertSame('42', $request->query->get('page'));
    }

    #[DataProvider('targetProvider')]
    public function testKeepsAnExistingTarget(string $key): void
    {
        $request = new Request([$key => '7']);

        ($this->listener('neo-digital.at'))(new PreviewUrlConvertEvent($request));

        $this->assertSame('7', $request->query->get($key));
        $this->assertSame('page' === $key ? '7' : null, $request->query->get('page'));
    }

    public static function targetProvider(): iterable
    {
        yield ['url'];
        yield ['article'];
        yield ['page'];
    }

    public function testDoesNothingWithoutADomain(): void
    {
        $request = new Request();

        ($this->listener(''))(new PreviewUrlConvertEvent($request));

        $this->assertFalse($request->query->has('page'));
    }

    private function listener(string $dns): PreviewStartPageListener
    {
        $root = $this->mockClassWithProperties(PageModel::class, ['id' => 42, 'type' => 'root', 'dns' => $dns, 'useSSL' => true, 'fallback' => true]);

        $adapter = $this->mockAdapter(['findPublishedRootPages']);
        $adapter->method('findPublishedRootPages')->willReturn([$root]);

        return new PreviewStartPageListener(new FrontendBaseUrl($this->mockContaoFramework([PageModel::class => $adapter])));
    }
}
