<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle\Tests;

use PHPUnit\Framework\TestCase;

final class TemplatesTest extends TestCase
{
    private const TEMPLATES = __DIR__.'/../contao/templates';

    /**
     * Ohne die Markierung behandelt Contao jeden Unterordner eines Bundles als
     * eigenen, flachen Vorlagen-Ordner — „backend/_neocms_brand" hieße dann nur
     * „_neocms_brand" und be_main/be_login fänden ihr Logo nicht (Fehler 500).
     */
    public function testTemplateFolderIsANamespaceRoot(): void
    {
        $this->assertFileExists(self::TEMPLATES.'/.twig-root');
    }

    public function testIncludedPartialsExist(): void
    {
        foreach (glob(self::TEMPLATES.'/*.html.twig') as $template) {
            preg_match_all("/include\\('@Contao\\/([^']+)'/", (string) file_get_contents($template), $matches);

            foreach ($matches[1] as $partial) {
                $this->assertFileExists(self::TEMPLATES.'/'.$partial, basename($template).' bindet '.$partial.' ein');
            }
        }
    }
}
