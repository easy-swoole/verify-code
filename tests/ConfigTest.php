<?php

declare(strict_types=1);

namespace EasySwoole\VerifyCode\Tests;

use EasySwoole\VerifyCode\Config;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    #[DataProvider('hexColorProvider')]
    public function testItParsesHexColors(string $hex, array $expected): void
    {
        $config = (new Config())->setFontColor($hex)->setBackColor($hex);

        self::assertSame($expected, $config->fontColor);
        self::assertSame($expected, $config->backColor);
    }

    public static function hexColorProvider(): iterable
    {
        yield 'three digits' => ['#abc', [170, 187, 204]];
        yield 'six digits' => ['3A5FCD', [58, 95, 205]];
    }

    public function testItAcceptsRgbColors(): void
    {
        $config = (new Config())->setFontColor([30, 144, 255]);

        self::assertSame([30, 144, 255], $config->fontColor);
    }

    #[DataProvider('invalidRgbProvider')]
    public function testItRejectsInvalidRgbColors(array $color): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Config())->setFontColor($color);
    }

    public static function invalidRgbProvider(): iterable
    {
        yield 'too few channels' => [[0, 0]];
        yield 'too many channels' => [[0, 0, 0, 0]];
        yield 'negative channel' => [[-1, 0, 0]];
        yield 'channel above 255' => [[0, 0, 256]];
    }

    #[DataProvider('invalidPositiveValueProvider')]
    public function testItRejectsNonPositiveDimensionsAndLengths(string $method): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Config())->{$method}(0);
    }

    public static function invalidPositiveValueProvider(): iterable
    {
        yield 'width' => ['setImageWidth'];
        yield 'height' => ['setImageHeight'];
        yield 'font size' => ['setFontSize'];
        yield 'length' => ['setLength'];
    }

    #[DataProvider('complexityLevelProvider')]
    public function testItAcceptsComplexityLevelsFromOneToNine(int $level): void
    {
        $config = (new Config())->setComplexityLevel($level);

        self::assertSame($level, $config->complexityLevel);
    }

    public static function complexityLevelProvider(): iterable
    {
        foreach (range(1, 9) as $level) {
            yield "level {$level}" => [$level];
        }
    }

    #[DataProvider('invalidComplexityLevelProvider')]
    public function testItRejectsInvalidComplexityLevels(int $level): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Config())->setComplexityLevel($level);
    }

    public static function invalidComplexityLevelProvider(): iterable
    {
        yield 'below minimum' => [0];
        yield 'above maximum' => [10];
    }

    public function testItConfiguresDecoyCodeCount(): void
    {
        self::assertSame(4, (new Config())->setDecoyCodeCount(4)->decoyCodeCount);
    }

    #[DataProvider('invalidDecoyCodeCountProvider')]
    public function testItRejectsInvalidDecoyCodeCounts(int $count): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Config())->setDecoyCodeCount($count);
    }

    public static function invalidDecoyCodeCountProvider(): iterable
    {
        yield 'below minimum' => [0];
        yield 'above maximum' => [6];
    }

    public function testItConfiguresDecoyCharacterCount(): void
    {
        self::assertSame(3, (new Config())->setDecoyCharacterCount(3)->decoyCharacterCount);
    }

    #[DataProvider('invalidDecoyCharacterCountProvider')]
    public function testItRejectsInvalidDecoyCharacterCounts(int $count): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Config())->setDecoyCharacterCount($count);
    }

    public static function invalidDecoyCharacterCountProvider(): iterable
    {
        yield 'below minimum' => [0];
        yield 'above maximum' => [9];
    }
}
