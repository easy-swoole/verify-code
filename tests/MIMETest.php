<?php

declare(strict_types=1);

namespace EasySwoole\VerifyCode\Tests;

use EasySwoole\VerifyCode\MIME;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MIMETest extends TestCase
{
    #[DataProvider('mimeProvider')]
    public function testItMapsMimeTypesToGdExtensions(string $mime, string $extension): void
    {
        self::assertSame($extension, MIME::getExtensionName($mime));
    }

    public static function mimeProvider(): iterable
    {
        yield 'png' => [MIME::PNG, 'png'];
        yield 'gif' => [MIME::GIF, 'gif'];
        yield 'jpeg' => [MIME::JPG, 'jpeg'];
    }

    public function testItRejectsUnsupportedMimeTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        MIME::getExtensionName('image/webp');
    }
}
