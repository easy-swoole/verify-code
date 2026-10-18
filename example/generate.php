<?php
declare(strict_types=1);

use EasySwoole\VerifyCode\{CaptchaColor, Config, MIME, VerifyCode};
require dirname(__DIR__) . '/vendor/autoload.php';

function baseConfig(): Config
{
    return (new Config())
        ->setUseFont(dirname(__DIR__) . '/src/assets/ttf/6.ttf')
        ->setImageWidth(320)->setImageHeight(100)->setFontSize(30)
        ->setFontColor('#2457A6')->setBackColor('#FFFFFF')
        ->setUseNoise(false)->setUseCurve(false)->setComplexityLevel(1);
}

function saveExample(string $name, Config $config, ?string $code = 'AbC729', ?CaptchaColor $color = null): void
{
    $result = (new VerifyCode($config))->drawCode($code, $color);
    if (getimagesizefromstring($result->getImageByte()) === false
        || file_put_contents(__DIR__ . '/' . $name, $result->getImageByte()) === false) {
        throw new RuntimeException('Failed to generate ' . $name);
    }
    echo $name . PHP_EOL;
}

saveExample('default.png', new Config(), null);
saveExample('clean.png', baseConfig());
saveExample('digits.png', baseConfig()->setCharset('0123456789')->setLength(6), null);
saveExample('charset.png', baseConfig()->setCharset('23456789ABCDEFGHJKLMNPQRSTUVWXYZ')->setLength(6), null);
saveExample('colors.png', baseConfig()->setFontColor('#7A2E8E')->setBackColor([255, 249, 240]));
saveExample('size.png', baseConfig()->setImageWidth(420)->setImageHeight(120)->setFontSize(38));
saveExample('noise.png', baseConfig()->setUseNoise(true));
saveExample('curves.png', baseConfig()->setUseCurve(true)->setComplexityLevel(4));
saveExample('combined.png', baseConfig()->setUseNoise(true)->setUseCurve(true)->setComplexityLevel(4));
foreach (range(1, 9) as $level) {
    saveExample('level-' . $level . '.png', baseConfig()->setComplexityLevel($level)->setUseNoise(true)->setUseCurve(true));
}
saveExample('level-7-clean.png', baseConfig()->setComplexityLevel(7));
foreach (CaptchaColor::cases() as $color) {
    saveExample('target-' . $color->value . '.png', baseConfig()->setComplexityLevel(3), 'AbC729', $color);
}
saveExample('decoys.png', baseConfig()->setComplexityLevel(3)->setDecoyCodeCount(1)->setDecoyCharacterCount(1), 'AbC729', CaptchaColor::BLUE);
foreach (range(1, 6) as $index) {
    saveExample('font-' . $index . '.png', baseConfig()->setUseFont(dirname(__DIR__) . '/src/assets/ttf/' . $index . '.ttf'));
}
foreach ([MIME::PNG => 'png', MIME::GIF => 'gif', MIME::JPG => 'jpg'] as $mime => $extension) {
    saveExample('format.' . $extension, baseConfig()->setMimeType($mime));
}
