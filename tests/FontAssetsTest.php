<?php

declare(strict_types=1);

namespace EasySwoole\VerifyCode\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FontAssetsTest extends TestCase
{
    private const CHARSET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    #[DataProvider('fontProvider')]
    public function testBundledFontRendersConfiguredCharacters(string $font, string $license): void
    {
        $fontPath = dirname(__DIR__) . '/src/assets/ttf/' . $font;
        $licensePath = dirname(__DIR__) . '/src/assets/ttf/licenses/' . $license;

        self::assertFileExists($fontPath);
        self::assertFileExists($licensePath);
        self::assertNotSame('', file_get_contents($licensePath));
        foreach (str_split(self::CHARSET) as $character) {
            self::assertIsArray(imagettfbbox(24, 0, $fontPath, $character));
        }
    }

    public static function fontProvider(): iterable
    {
        yield 'Black Ops One' => ['1.ttf', 'BlackOpsOne-OFL.txt'];
        yield 'Bungee' => ['2.ttf', 'Bungee-OFL.txt'];
        yield 'Monoton' => ['3.ttf', 'Monoton-OFL.txt'];
        yield 'Wallpoet' => ['4.ttf', 'Wallpoet-OFL.txt'];
        yield 'Jolly Lodger' => ['5.ttf', 'JollyLodger-OFL.txt'];
        yield 'Play Bold' => ['6.ttf', 'Play-OFL.txt'];
    }
}
