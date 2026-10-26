<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle\EventListener;

use Neo\ContaoNeocmsBundle\Routing\FrontendBaseUrl;
use Contao\CoreBundle\DataContainer\DataContainerOperation;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Vorschau-Links (System → Vorschau-Links) auf der Hauptdomain statt auf der
 * Admin-Subdomain.
 *
 * Contao baut die teilbare Adresse /_contao/preview/{id} mit der Domain, auf der
 * man gerade arbeitet — hier admin.neo-digital.at. Kunden sollen aber einen Link
 * auf neo-digital.at bekommen. Das ist nicht nur Kosmetik: Der Link meldet den
 * Besucher für die Vorschau an und leitet dann zur Zielseite weiter; die Anmeldung
 * gilt nur auf der Domain, auf der der Link geöffnet wurde. Deshalb nimmt der Link
 * die Domain seiner Zielseite (Feld „url"), ersatzweise die des Startpunkts.
 *
 * Contao erzeugt die Adresse an zwei Stellen, beide in seinem internen
 * PreviewLinkListener: am Teilen-Knopf der Liste und im Hinweis der Bearbeiten-
 * Ansicht. Beide werden hier ersetzt — der Knopf über die höhere Priorität (bei
 * Einzel-Callbacks gewinnt sie), der Hinweis, indem Contaos onload-Callback aus
 * der DCA genommen wird. Die übrige Logik entspricht Contao 5.7; bei einem
 * Update mit vendor/contao/core-bundle/src/EventListener/DataContainer/
 * PreviewLinkListener.php abgleichen.
 */
final class PreviewLinkHostListener
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UriSigner $uriSigner,
        private readonly ClockInterface $clock,
        private readonly FrontendBaseUrl $frontendBaseUrl,
    ) {
    }

    #[AsCallback(table: 'tl_preview_link', target: 'list.operations.share.button', priority: 10)]
    public function shareOperation(DataContainerOperation $operation): void
    {
        $row = $operation->getRecord();

        if ($row['expiresAt'] < $this->clock->now()->getTimestamp()) {
            $operation->disable();

            return;
        }

        $url = $this->shareUrl($row);

        $operation->setUrl($url);
        $operation['attributes']
            ->set('data-controller', 'contao--clipboard')
            ->set('data-contao--clipboard-content-value', $url)
            ->set('data-contao--clipboard-message-value', $this->translator->trans('MSC.clipboardCopy', [], 'contao_default'))
            ->set('data-action', 'contao--clipboard#write:prevent')
        ;
    }

    /**
     * Contaos Hinweis mit der Link-Adresse aus der Bearbeiten-Ansicht nehmen —
     * an seine Stelle tritt adjustEditView() unten. Läuft nach Contaos
     * DataContainerCallbackListener (Priorität -16), der die Callbacks einträgt.
     */
    #[AsHook('loadDataContainer', priority: -32)]
    public function replaceEditHint(string $table): void
    {
        if ('tl_preview_link' !== $table) {
            return;
        }

        $callbacks = &$GLOBALS['TL_DCA']['tl_preview_link']['config']['onload_callback'];

        foreach ($callbacks ?? [] as $i => $callback) {
            if (\is_array($callback) && 'contao.listener.data_container.preview_link' === $callback[0] && 'adjustEditView' === $callback[1]) {
                unset($callbacks[$i]);
            }
        }
    }

    #[AsCallback(table: 'tl_preview_link', target: 'config.onload')]
    public function adjustEditView(DataContainer $dc): void
    {
        if ('edit' !== $this->framework->getAdapter(Input::class)->get('act')) {
            return;
        }

        $message = $this->framework->getAdapter(Message::class);
        $row = $dc->getCurrentRecord();

        if ($row['expiresAt'] < $this->clock->now()->getTimestamp()) {
            $message->addError($this->translator->trans('tl_preview_link.hintExpired', [], 'contao_tl_preview_link'));
        } elseif (0 === (int) $row['tstamp']) {
            $message->addNew($this->translator->trans('tl_preview_link.hintSave', [], 'contao_tl_preview_link'));
        } else {
            $url = $this->shareUrl($row);

            $message->addInfo(\sprintf(
                '%s: <a href="%s" target="_blank" title="%s" data-controller="contao--clipboard" data-contao--clipboard-content-value="%s" data-contao--clipboard-message-value="%s" data-action="contao--clipboard#write:prevent">%s</a> ',
                $this->translator->trans('tl_preview_link.hintEdit', [], 'contao_tl_preview_link'),
                StringUtil::specialcharsUrl($url),
                StringUtil::specialchars($this->translator->trans('tl_preview_link.share.0', [], 'contao_tl_preview_link')),
                StringUtil::specialcharsUrl($url),
                $this->translator->trans('MSC.clipboardCopy', [], 'contao_default'),
                $url,
            ));
        }
    }

    /**
     * Signierte Adresse auf der Domain der Zielseite. Ohne absolute Zielseite und
     * ohne Domain am Startpunkt bleibt es bei Contaos Adresse (aktuelle Domain).
     */
    private function shareUrl(array $row): string
    {
        $base = $this->hostOf((string) $row['url']) ?? $this->frontendBaseUrl->get();

        $url = null === $base
            ? $this->urlGenerator->generate('contao_preview_link', ['id' => (int) $row['id']], UrlGeneratorInterface::ABSOLUTE_URL)
            : $base.$this->urlGenerator->generate('contao_preview_link', ['id' => (int) $row['id']]);

        return $this->uriSigner->sign($url);
    }

    private function hostOf(string $url): string|null
    {
        $parts = parse_url($url);

        if (empty($parts['host'])) {
            return null;
        }

        return ($parts['scheme'] ?? 'https').'://'.$parts['host'].(isset($parts['port']) ? ':'.$parts['port'] : '');
    }
}
