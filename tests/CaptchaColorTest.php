<?php

declare(strict_types=1);

namespace EasySwoole\VerifyCode\Tests;

use EasySwoole\VerifyCode\CaptchaColor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CaptchaColorTest extends TestCase
{
    #[DataProvider('colorProvider')]
    public function testEveryColorHasSafeDecoyColors(CaptchaColor $target): void
    {
        $decoys = $target->safeDecoyColors();

        self::assertNotEmpty($decoys);
        self::assertNotContains($target, $decoys);
        self::assertSame($decoys, array_values(array_unique($decoys, SORT_REGULAR)));
    }

    public static function colorProvider(): iterable
    {
        foreach (CaptchaColor::cases() as $color) {
            yield $color->value => [$color];
        }
    }

    #[DataProvider('confusingPairProvider')]
    public function testCommonConfusingPairsAreNotUsed(CaptchaColor $target, CaptchaColor $unsafe): void
    {
        self::assertNotContains($unsafe, $target->safeDecoyColors());
    }

    public static function confusingPairProvider(): iterable
    {
        yield 'red and green' => [CaptchaColor::RED, CaptchaColor::GREEN];
        yield 'green and red' => [CaptchaColor::GREEN, CaptchaColor::RED];
        yield 'red and orange' => [CaptchaColor::RED, CaptchaColor::ORANGE];
        yield 'orange and red' => [CaptchaColor::ORANGE, CaptchaColor::RED];
        yield 'blue and purple' => [CaptchaColor::BLUE, CaptchaColor::PURPLE];
        yield 'purple and blue' => [CaptchaColor::PURPLE, CaptchaColor::BLUE];
    }
}
