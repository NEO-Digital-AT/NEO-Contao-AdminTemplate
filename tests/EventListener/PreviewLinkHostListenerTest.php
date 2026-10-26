<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle\Tests\EventListener;

use Contao\DataContainer;
use Contao\Input;
use Contao\Message;
use Contao\PageModel;
use Contao\System;
use Contao\TestCase\ContaoTestCase;
use Neo\ContaoNeocmsBundle\EventListener\PreviewLinkHostListener;
use Neo\ContaoNeocmsBundle\Routing\FrontendBaseUrl;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

// Die Contao-Hilfen (mockAdapter, mockContaoFramework) liefern Mocks ohne Erwartungen.
#[AllowMockObjectsWithoutExpectations]
final class PreviewLinkHostListenerTest extends ContaoTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // StringUtil (Ausgabe des Hinweises) liest den Zeichensatz aus dem Container
        System::setContainer($this->getContainerWithContaoConfiguration());
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TL_DCA']);

        parent::tearDown();
    }

    public function testRemovesContaosEditHintOnly(): void
    {
        $GLOBALS['TL_DCA']['tl_preview_link']['config']['onload_callback'] = [
            ['contao.listener.data_container.preview_link', 'createFromUrl'],
            ['contao.listener.data_container.preview_link', 'adjustEditView'],
            [PreviewLinkHostListener::class, 'adjustEditView'],
        ];

        $this->listener('', $this->mockAdapter(['addInfo']))->replaceEditHint('tl_preview_link');

        $this->assertSame(
            [
                ['contao.listener.data_container.preview_link', 'createFromUrl'],
                [PreviewLinkHostListener::class, 'adjustEditView'],
            ],
            array_values($GLOBALS['TL_DCA']['tl_preview_link']['config']['onload_callback']),
        );
    }

    public function testIgnoresOtherTables(): void
    {
        $GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'] = [['contao.listener.data_container.preview_link', 'adjustEditView']];

        $this->listener('', $this->mockAdapter(['addInfo']))->replaceEditHint('tl_content');

        $this->assertCount(1, $GLOBALS['TL_DCA']['tl_content']['config']['onload_callback']);
    }

    #[DataProvider('hostProvider')]
    public function testSharesTheLinkOnTheDomainOfItsTarget(string $targetUrl, string $rootDns, string $expectedPrefix): void
    {
        $message = $this->mockAdapter(['addInfo', 'addError', 'addNew']);
        $message
            ->expects($this->once())
            ->method('addInfo')
            ->with($this->callback(static fn (string $html): bool => str_contains($html, '>'.$expectedPrefix.'/_contao/preview/5?_hash=')))
        ;

        $dc = $this->createStub(DataContainer::class);
        $dc->method('getCurrentRecord')->willReturn(['id' => 5, 'url' => $targetUrl, 'tstamp' => 1, 'expiresAt' => 2_000_000_000]);

        $this->listener($rootDns, $message)->adjustEditView($dc);
    }

    public static function hostProvider(): iterable
    {
        yield 'absolute target wins' => ['https://neo-digital.at/preview.php/leistungen', 'other.at', 'https://neo-digital.at'];
        yield 'target with port' => ['http://localhost:8095/preview.php/', '', 'http://localhost:8095'];
        yield 'relative target uses the root page domain' => ['/preview.php/', 'neo-digital.at', 'https://neo-digital.at'];
        yield 'neither falls back to the current domain' => ['/preview.php/', '', 'https://admin.neo-digital.at'];
    }

    private function listener(string $rootDns, object $message): PreviewLinkHostListener
    {
        $root = $this->mockClassWithProperties(PageModel::class, ['id' => 1, 'type' => 'root', 'dns' => $rootDns, 'useSSL' => true, 'fallback' => true]);
        $pages = $this->mockAdapter(['findPublishedRootPages']);
        $pages->method('findPublishedRootPages')->willReturn([$root]);

        $input = $this->mockAdapter(['get']);
        $input->method('get')->willReturn('edit');

        $framework = $this->mockContaoFramework([PageModel::class => $pages, Input::class => $input, Message::class => $message]);

        $urlGenerator = $this->createStub(UrlGeneratorInterface::class);
        $urlGenerator
            ->method('generate')
            ->willReturnCallback(static fn (string $route, array $params, int $type = UrlGeneratorInterface::ABSOLUTE_PATH): string => (UrlGeneratorInterface::ABSOLUTE_URL === $type ? 'https://admin.neo-digital.at' : '').'/_contao/preview/'.$params['id'])
        ;

        $translator = $this->createStub(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return new PreviewLinkHostListener(
            $framework,
            $translator,
            $urlGenerator,
            new UriSigner('secret'),
            new MockClock('2026-10-26 12:00:00'),
            new FrontendBaseUrl($framework),
        );
    }
}
