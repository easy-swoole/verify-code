<?php

declare(strict_types=1);

namespace EasySwoole\VerifyCode\Tests;

use EasySwoole\VerifyCode\MIME;
use EasySwoole\VerifyCode\Result;
use EasySwoole\VerifyCode\CaptchaColor;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{
    public function testItExposesCaptchaData(): void
    {
        $result = new Result('image-bytes', 'ABCD', MIME::PNG);

        self::assertSame('image-bytes', $result->getImageByte());
        self::assertSame('ABCD', $result->getImageCode());
        self::assertSame(MIME::PNG, $result->getImageMime());
        self::assertSame('data:image/png;base64,' . base64_encode('image-bytes'), $result->getImageBase64());
        self::assertLessThanOrEqual(time(), $result->getCreateTime());
    }

    public function testHashUsesExplicitFalseyValues(): void
    {
        $result = new Result('image-bytes', 'ABCD', MIME::PNG);

        self::assertSame(substr(md5('01'), 8, 16), $result->getCodeHash('0', 1));
    }

    public function testItExposesTargetColorAndPrompt(): void
    {
        $result = new Result('image-bytes', 'ABCD', MIME::PNG, CaptchaColor::BLUE);

        self::assertSame(CaptchaColor::BLUE, $result->getTargetColor());
        self::assertSame('请输入蓝色的验证码', $result->getPrompt());
    }
}
