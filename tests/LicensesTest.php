<?php

declare(strict_types=1);

namespace Neo\ContaoNeocmsBundle\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Mitgelieferte Schriften und Icons dürfen nur mit ihrer Lizenz weitergegeben
 * werden (OFL bzw. MIT verlangen den Text im Paket).
 */
final class LicensesTest extends TestCase
{
    private const PUBLIC = __DIR__.'/../public';

    public function testSpaceGroteskShipsWithTheOpenFontLicense(): void
    {
        $ofl = (string) file_get_contents(self::PUBLIC.'/fonts/OFL.txt');

        $this->assertStringStartsWith('Copyright 2020 The Space Grotesk Project Authors', $ofl);
        $this->assertStringContainsString('SIL OPEN FONT LICENSE Version 1.1', $ofl);
        $this->assertStringNotContainsString('<Copyright Holder>', $ofl);
    }

    public function testFluentIconsShipWithTheMitLicense(): void
    {
        $mit = (string) file_get_contents(self::PUBLIC.'/icons/fluent/LICENSE.txt');

        $this->assertStringContainsString('Copyright (c) 2020 Microsoft Corporation', $mit);
        $this->assertStringContainsString('MIT License', $mit);
    }
}
