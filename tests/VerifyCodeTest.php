<?php

declare(strict_types=1);

namespace EasySwoole\VerifyCode\Tests;

use EasySwoole\VerifyCode\Config;
use EasySwoole\VerifyCode\CaptchaColor;
use EasySwoole\VerifyCode\MIME;
use EasySwoole\VerifyCode\VerifyCode;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class VerifyCodeTest extends TestCase
{
    #[DataProvider('mimeProvider')]
    public function testItGeneratesSupportedImageFormats(string $mime, int $expectedImageType): void
    {
        $config = (new Config())
            ->setMimeType($mime)
            ->setLength(6)
            ->setUseCurve()
            ->setImageWidth(180)
            ->setImageHeight(60)
            ->setFontColor('#abc')
            ->setBackColor([255, 255, 255]);

        $result = (new VerifyCode($config))->drawCode();
        $size = getimagesizefromstring($result->getImageByte());

        self::assertSame(6, strlen($result->getImageCode()));
        self::assertSame($mime, $result->getImageMime());
        self::assertIsArray($size);
        self::assertSame(180, $size[0]);
        self::assertSame(60, $size[1]);
        self::assertSame($expectedImageType, $size[2]);
    }

    public static function mimeProvider(): iterable
    {
        yield 'png' => [MIME::PNG, IMAGETYPE_PNG];
        yield 'gif' => [MIME::GIF, IMAGETYPE_GIF];
        yield 'jpeg' => [MIME::JPG, IMAGETYPE_JPEG];
    }

    public function testItUsesOnlyConfiguredCharacters(): void
    {
        $config = (new Config())->setCharset('AB')->setLength(20);

        $code = (new VerifyCode($config))->drawCode()->getImageCode();

        self::assertSame(20, strlen($code));
        self::assertMatchesRegularExpression('/^[AB]+$/', $code);
    }

    public function testItPreservesExplicitCodeAndCanBeReused(): void
    {
        $generator = new VerifyCode();

        self::assertSame('ABCD', $generator->drawCode('ABCD')->getImageCode());
        self::assertSame('EFGH', $generator->drawCode('EFGH')->getImageCode());
    }

    public function testBuiltInFontsDoNotMutateTheConfiguration(): void
    {
        $config = new Config();

        new VerifyCode($config);
        new VerifyCode($config);

        self::assertSame([], $config->fonts);
    }

    public function testItSupportsTallImagesWithCurves(): void
    {
        $config = (new Config())->setImageWidth(20)->setImageHeight(100)->setUseCurve();
        $result = (new VerifyCode($config))->drawCode('A');

        self::assertNotSame('', $result->getImageByte());
    }

    public function testHighComplexityDoesNotRequireNoiseOrCurves(): void
    {
        $config = (new Config())
            ->setComplexityLevel(9)
            ->setUseNoise(false)
            ->setUseCurve(false);

        $result = (new VerifyCode($config))->drawCode('TEST');

        self::assertNotFalse(getimagesizefromstring($result->getImageByte()));
        self::assertFalse($config->useNoise);
        self::assertFalse($config->useCurve);
    }

    public function testItRejectsEmptyExplicitCode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new VerifyCode())->drawCode('');
    }

    public function testItRejectsEmptyCharsetForGeneratedCode(): void
    {
        $config = (new Config())->setCharset('');
        $this->expectException(InvalidArgumentException::class);

        (new VerifyCode($config))->drawCode();
    }

    #[DataProvider('complexityLevelProvider')]
    public function testItGeneratesAnImageAtEveryComplexityLevel(int $level): void
    {
        $config = (new Config())->setComplexityLevel($level);
        $result = (new VerifyCode($config))->drawCode('TEST');

        self::assertNotFalse(getimagesizefromstring($result->getImageByte()));
    }

    public static function complexityLevelProvider(): iterable
    {
        foreach (range(1, 9) as $level) {
            yield "level {$level}" => [$level];
        }
    }

    #[DataProvider('colorProvider')]
    public function testItGeneratesColorTargetedCaptchas(CaptchaColor $color): void
    {
        $config = (new Config())->setDecoyCodeCount(3)->setDecoyCharacterCount(2);
        $result = (new VerifyCode($config))->drawCode('ABCD', $color);

        self::assertSame('ABCD', $result->getImageCode());
        self::assertSame($color, $result->getTargetColor());
        self::assertSame("请输入{$color->label()}的验证码", $result->getPrompt());
        self::assertNotFalse(getimagesizefromstring($result->getImageByte()));
    }

    public static function colorProvider(): iterable
    {
        foreach (CaptchaColor::cases() as $color) {
            yield $color->value => [$color];
        }
    }
}
