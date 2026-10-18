<?php

namespace EasySwoole\VerifyCode;

/**
 * 验证码处理类
 */
class VerifyCode
{
    private const BUILT_IN_FONTS = [
        'Black Ops One' => '1.ttf',
        'Bungee' => '2.ttf',
        'Monoton' => '3.ttf',
        'Wallpoet' => '4.ttf',
        'Jolly Lodger' => '5.ttf',
        'Play Bold' => '6.ttf',
    ];

    protected Config $conf;
    protected \GdImage $imInstance;

    protected string $useFont;

    public function __construct(?Config $option = null)
    {
        $this->conf = $option ?? new Config();

        if ($this->conf->useFont) {
            $this->useFont = $this->conf->useFont;
        } else {
            $builtInFonts = array_map(
                static fn (string $file): string => __DIR__ . '/assets/ttf/' . $file,
                self::BUILT_IN_FONTS
            );
            $fonts = array_values(array_merge($this->conf->fonts, $builtInFonts));
            $this->useFont = $fonts[array_rand($fonts)];
        }

        $this->conf->fontColor ??= [random_int(1, 150), random_int(1, 150), random_int(1, 150)];
        $this->conf->backColor ??= [255, 255, 255];
    }

    public function drawCode(?string $code = null, ?CaptchaColor $targetColor = null): Result
    {
        $code ??= $this->generateCode();
        if ($code === '') {
            throw new \InvalidArgumentException('Captcha code cannot be empty.');
        }

        $codeLen = strlen($code);
        $imageWidth = $this->conf->imageL ?? (int) ceil($codeLen * $this->conf->fontSize * 1.5 + $this->conf->fontSize / 2);
        $imageHeight = $this->conf->imageH ?? $this->conf->fontSize * 2;
        if ($imageWidth <= 0 || $imageHeight <= 0) {
            throw new \InvalidArgumentException('Image width and height must be greater than zero.');
        }

        // 创建空白画布
        $this->imInstance = imagecreate($imageWidth, $imageHeight);
        // 设置背景颜色
        imagecolorallocate($this->imInstance, ...$this->conf->backColor);
        // 设置字体颜色
        $fontColor = imagecolorallocate($this->imInstance, ...($targetColor?->rgb() ?? $this->conf->fontColor));
        // 画干扰噪点
        if ($this->conf->useNoise) $this->writeNoise($imageWidth, $imageHeight, $this->conf->complexityLevel);
        // 画干扰曲线
        $curveCount = $this->conf->useCurve
            ? max(1, (int) ceil($this->conf->complexityLevel / 3))
            : 0;
        for ($i = 0; $i < $curveCount; $i++) {
            $this->writeCurve($imageWidth, $imageHeight, $fontColor);
        }
        if ($this->conf->useCurve) {
            $this->writeInterferenceLines($imageWidth, $imageHeight, intdiv($this->conf->complexityLevel, 3));
        }

        if ($targetColor !== null) {
            $decoyColors = $targetColor->safeDecoyColors();
            shuffle($decoyColors);
            for ($i = 0; $i < $this->conf->decoyCodeCount; $i++) {
                $decoyColor = imagecolorallocate($this->imInstance, ...$decoyColors[$i % count($decoyColors)]->rgb());
                $this->drawDecoyCharacters($codeLen, $decoyColor, $imageWidth, $imageHeight);
            }
        }
        // 目标验证码最后绘制，避免被彩色干扰字符完全覆盖
        $this->drawCharacters($code, $fontColor, $imageWidth, $imageHeight);

        // 输出验证码结果集
        $func = 'image' . MIME::getExtensionName($this->conf->mime);
        ob_start();
        try {
            $func($this->imInstance);
            $file = (string) ob_get_contents();
        } finally {
            ob_end_clean();
            imagedestroy($this->imInstance);
        }
        return new Result($file, $code, $this->conf->mime, $targetColor);
    }

    private function generateCode(?int $length = null): string
    {
        if ($this->conf->charset === '') {
            throw new \InvalidArgumentException('Captcha charset cannot be empty.');
        }

        $code = '';
        $lastIndex = strlen($this->conf->charset) - 1;
        for ($i = 0; $i < ($length ?? $this->conf->length); $i++) {
            $code .= $this->conf->charset[random_int(0, $lastIndex)];
        }
        return $code;
    }

    private function drawCharacters(
        string $code,
        int $color,
        int $imageWidth,
        int $imageHeight
    ): void {
        $codeLen = strlen($code);
        $cellWidth = $imageWidth / $codeLen;
        $level = $this->conf->complexityLevel;
        for ($i = 0; $i < $codeLen; $i++) {
            $fontVariation = intdiv(($level - 1) * $this->conf->fontSize, 45);
            $fontSize = $this->conf->fontSize + ($fontVariation > 0 ? random_int(-$fontVariation, $fontVariation) : 0);
            $horizontalJitter = (int) min($cellWidth * 0.18, $level * 1.5);
            $verticalJitter = min((int) ($imageHeight * 0.18), $level * 2);
            $codeNX = (int) (($i + 0.18) * $cellWidth)
                + ($horizontalJitter > 0 ? random_int(-$horizontalJitter, $horizontalJitter) : 0);
            $codeNY = max($fontSize, min(
                $imageHeight - 2,
                (int) ($imageHeight * 0.72) + random_int(-$verticalJitter, $verticalJitter)
            ));
            $maxAngle = 10 + $level * 5;
            imagettftext(
                $this->imInstance,
                $fontSize,
                random_int(-$maxAngle, $maxAngle),
                $codeNX,
                $codeNY,
                $color,
                $this->useFont,
                $code[$i]
            );
        }
    }

    private function drawDecoyCharacters(
        int $targetLength,
        int $color,
        int $imageWidth,
        int $imageHeight
    ): void {
        $count = min($this->conf->decoyCharacterCount, $targetLength);
        $characters = $this->generateCode($count);
        $positions = range(0, $targetLength - 1);
        shuffle($positions);
        $cellWidth = $imageWidth / $targetLength;
        $fontSize = max(8, (int) ($this->conf->fontSize * 0.82));
        $maxOffsetX = max(2, (int) ($cellWidth * 0.12));
        $maxOffsetY = max(3, (int) ($imageHeight * 0.12));
        $maxAngle = 20 + $this->conf->complexityLevel * 5;

        for ($i = 0; $i < $count; $i++) {
            $targetPosition = $positions[$i];
            $x = (int) (($targetPosition + 0.18) * $cellWidth) + random_int(-$maxOffsetX, $maxOffsetX);
            $y = max($fontSize, min(
                $imageHeight - 2,
                (int) ($imageHeight * 0.72) + random_int(-$maxOffsetY, $maxOffsetY)
            ));
            imagettftext(
                $this->imInstance,
                $fontSize,
                random_int(-$maxAngle, $maxAngle),
                $x,
                $y,
                $color,
                $this->useFont,
                $characters[$i]
            );
        }
    }

    /**
     * 画干扰杂点
     * @author : evalor <master@evalor.cn>
     */
    private function writeNoise(int $imageWidth, int $imageHeight, int $level): void
    {
        $codeSet = '2345678abcdefhijkmnpqrstuvwxyz';
        $groups = 4 + $level * 2;
        $charactersPerGroup = 2 + (int) ceil($level / 3);
        for ($i = 0; $i < $groups; $i++) {
            $noiseColor = imagecolorallocate($this->imInstance, random_int(150, 225), random_int(150, 225), random_int(150, 225));
            for ($j = 0; $j < $charactersPerGroup; $j++) {
                // 绘杂点
                imagestring($this->imInstance, 5, random_int(-10, $imageWidth), random_int(-10, $imageHeight), $codeSet[random_int(0, strlen($codeSet) - 1)], $noiseColor);
            }
        }
    }

    private function writeInterferenceLines(int $imageWidth, int $imageHeight, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $channels = array_map(
                static fn (int $channel): int => max(0, min(255, $channel + random_int(-55, 55))),
                $this->conf->fontColor
            );
            $color = imagecolorallocate($this->imInstance, ...$channels);
            $startX = random_int(0, intdiv($imageWidth, 4));
            $startY = random_int(0, $imageHeight - 1);
            $controlX = random_int(intdiv($imageWidth, 3), (int) ($imageWidth * 0.66));
            $controlY = random_int(0, $imageHeight - 1);
            $endX = random_int((int) ($imageWidth * 0.75), $imageWidth - 1);
            $endY = random_int(0, $imageHeight - 1);
            $previousX = $startX;
            $previousY = $startY;
            for ($step = 1; $step <= 48; $step++) {
                $t = $step / 48;
                $inverse = 1 - $t;
                $x = (int) ($inverse * $inverse * $startX + 2 * $inverse * $t * $controlX + $t * $t * $endX);
                $y = (int) ($inverse * $inverse * $startY + 2 * $inverse * $t * $controlY + $t * $t * $endY);
                imageline($this->imInstance, $previousX, $previousY, $x, $y, $color);
                $previousX = $x;
                $previousY = $y;
            }
        }
    }

    /**
     * 画干扰曲线
     * @author : evalor <master@evalor.cn>
     */
    private function writeCurve(int $imageWidth, int $imageHeight, int $fontColor): void
    {
        if ($imageWidth < 2 || $imageHeight < 2) {
            return;
        }

        $px = $py = 0;
        // 曲线前部分
        $A = random_int(1, intdiv($imageHeight, 2)); // 振幅
        $b = random_int(-intdiv($imageHeight, 4), intdiv($imageHeight, 4)); // Y轴方向偏移量
        $f = random_int(-intdiv($imageHeight, 4), intdiv($imageHeight, 4)); // X轴方向偏移量
        $T = random_int(min($imageHeight, $imageWidth * 2), max($imageHeight, $imageWidth * 2)); // 周期
        $w = (2 * M_PI) / $T;
        $px1 = 0; // 曲线横坐标起始位置
        $px2 = random_int(intdiv($imageWidth, 2), (int) ($imageWidth * 0.8)); // 曲线横坐标结束位置
        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $imageHeight / 2; // y = Asin(ωx+φ) + b
                imagesetpixel($this->imInstance, (int) $px, (int) $py, $fontColor);
            }
        }
        // 曲线后部分
        $A = random_int(1, intdiv($imageHeight, 2)); // 振幅
        $f = random_int(-intdiv($imageHeight, 4), intdiv($imageHeight, 4)); // X轴方向偏移量
        $T = random_int(min($imageHeight, $imageWidth * 2), max($imageHeight, $imageWidth * 2)); // 周期
        $w = (2 * M_PI) / $T;
        $b = $py - $A * sin($w * $px + $f) - $imageHeight / 2;
        $px1 = $px2;
        $px2 = $imageWidth;
        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $imageHeight / 2; // y = Asin(ωx+φ) + b
                imagesetpixel($this->imInstance, (int) $px, (int) $py, $fontColor);
            }
        }
    }
}
