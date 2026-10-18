<?php
// +----------------------------------------------------------------------
// | easySwoole [ use swoole easily just like echo "hello world" ]
// +----------------------------------------------------------------------
// | WebSite: https://www.easyswoole.com
// +----------------------------------------------------------------------
// | Welcome Join QQGroup 633921431
// +----------------------------------------------------------------------

namespace EasySwoole\VerifyCode;

use EasySwoole\Spl\SplBean;


class Config extends SplBean
{

    public string $charset = '1234567890AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz'; // 字母表
    public bool $useCurve = false;     // 混淆曲线
    public bool $useNoise = true;      // 随机噪点
    public ?string $useFont = null;    // 指定字体
    /** @var array{int, int, int}|null */
    public ?array $fontColor = null;
    /** @var array{int, int, int}|null */
    public ?array $backColor = null;
    public ?int $imageL = null;        // 图片宽度
    public ?int $imageH = null;        // 图片高度
    /** @var list<string> */
    public array $fonts     = [];            // 额外字体
    public int $fontSize  = 25;            // 字体大小
    public int $length    = 4;             // 验证码长度
    public int $complexityLevel = 1;       // 复杂度等级（1-9）
    public int $decoyCodeCount = 2;        // 彩色模式下的干扰验证码数量
    public int $decoyCharacterCount = 2;   // 每组干扰字符数量
    public string $mime = MIME::PNG;   // 设置类型

    /**
     * 设置图片格式
     * @param $MimeType
     * @return Config
     *@author : evalor <master@evalor.cn>
     */
    public function setMimeType(string $MimeType): static
    {
        $allowMime = [ MIME::PNG, MIME::GIF, MIME::JPG ];
        if (in_array($MimeType, $allowMime)) $this->mime = $MimeType;
        return $this;
    }

    /**
     * 设置字符集
     * @param string $charset
     * @return Config
     */
    public function setCharset(string $charset): static
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * 开启混淆曲线
     * @param bool $useCurve
     * @return Config
     */
    public function setUseCurve(bool $useCurve = true): static
    {
        $this->useCurve = $useCurve;
        return $this;
    }

    /**
     * 开启噪点生成
     * @param bool $useNoise
     * @return Config
     */
    public function setUseNoise(bool $useNoise = true): static
    {
        $this->useNoise = $useNoise;
        return $this;
    }

    /**
     * 使用自定义字体
     * @param string $useFont
     * @return Config
     */
    public function setUseFont(string $useFont): static
    {
        $this->useFont = $useFont;
        return $this;
    }

    /**
     * 设置文字颜色
     * @param array|string $fontColor
     * @return Config
     */
    public function setFontColor(array|string $fontColor): static
    {
        if (is_string($fontColor)) $this->fontColor = $this->HEXToRGB($fontColor);
        if (is_array($fontColor)) $this->fontColor = $this->validateRGB($fontColor);
        return $this;
    }

    /**
     * 设置背景颜色
     * @param null $backColor
     * @return Config
     */
    public function setBackColor(array|string $backColor): static
    {
        if (is_string($backColor)) $this->backColor = $this->HEXToRGB($backColor);
        if (is_array($backColor)) $this->backColor = $this->validateRGB($backColor);
        return $this;
    }

    /**
     * 设置图片宽度
     * @param int|string $imageL
     * @return Config
     */
    public function setImageWidth(int|string $imageL): static
    {
        $imageL = intval($imageL);
        if ($imageL <= 0) {
            throw new \InvalidArgumentException('Image width must be greater than zero.');
        }
        $this->imageL = $imageL;
        return $this;
    }

    /**
     * 设置图片高度
     * @param null $imageH
     * @return Config
     */
    public function setImageHeight(int|string $imageH): static
    {
        $imageH = intval($imageH);
        if ($imageH <= 0) {
            throw new \InvalidArgumentException('Image height must be greater than zero.');
        }
        $this->imageH = $imageH;
        return $this;
    }

    /**
     * 设置字体集
     * @param array|string $fonts
     * @return Config
     */
    public function addFonts(array|string $fonts): static
    {
        if (is_string($fonts)) {
            $this->fonts[] = $fonts;
        }
        if (is_array($fonts) && !empty($fonts)) {
            if (empty($this->fonts)) {
                $this->fonts = $fonts;
            } else {
                $this->fonts = array_merge($this->fonts, $fonts);
            }
        }
        return $this;
    }

    /**
     * 设置字体尺寸
     * @param int $fontSize
     * @return Config
     */
    public function setFontSize(int|string $fontSize): static
    {
        $fontSize = intval($fontSize);
        if ($fontSize <= 0) {
            throw new \InvalidArgumentException('Font size must be greater than zero.');
        }
        $this->fontSize = $fontSize;
        return $this;
    }

    public function setLength(int|string $length): static
    {
        $length = intval($length);
        if ($length <= 0) {
            throw new \InvalidArgumentException('Captcha length must be greater than zero.');
        }
        $this->length = $length;
        return $this;
    }

    /**
     * 设置验证码复杂度，1 最易识别，9 干扰最强
     */
    public function setComplexityLevel(int|string $level): static
    {
        $level = intval($level);
        if ($level < 1 || $level > 9) {
            throw new \InvalidArgumentException('Complexity level must be between 1 and 9.');
        }
        $this->complexityLevel = $level;
        return $this;
    }

    public function setDecoyCodeCount(int|string $count): static
    {
        $count = intval($count);
        if ($count < 1 || $count > 5) {
            throw new \InvalidArgumentException('Decoy code count must be between 1 and 5.');
        }
        $this->decoyCodeCount = $count;
        return $this;
    }

    public function setDecoyCharacterCount(int|string $count): static
    {
        $count = intval($count);
        if ($count < 1 || $count > 8) {
            throw new \InvalidArgumentException('Decoy character count must be between 1 and 8.');
        }
        $this->decoyCharacterCount = $count;
        return $this;
    }


    /**
     * 十六进制转RGB
     * @param $hexColor
     * @author : evalor <master@evalor.cn>
     * @return array
     */
    private function HEXToRGB(string $hexColor): array
    {
        $color = ltrim($hexColor, '#');
        if (!preg_match('/^(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color)) {
            throw new \InvalidArgumentException('Color must be a 3- or 6-digit hexadecimal value.');
        }
        if (strlen($color) === 6) {
            $rgb = array(
                hexdec(substr($color, 0, 2)),
                hexdec(substr($color, 2, 2)),
                hexdec(substr($color, 4, 2))
            );
        } else {
            $r = substr($color, 0, 1) . substr($color, 0, 1);
            $g = substr($color, 1, 1) . substr($color, 1, 1);
            $b = substr($color, 2, 1) . substr($color, 2, 1);
            $rgb = array(
                hexdec($r),
                hexdec($g),
                hexdec($b)
            );
        }
        return $rgb;
    }

    private function validateRGB(array $color): array
    {
        if (count($color) !== 3) {
            throw new \InvalidArgumentException('RGB color must contain exactly three values.');
        }

        $color = array_values(array_map('intval', $color));
        foreach ($color as $value) {
            if ($value < 0 || $value > 255) {
                throw new \InvalidArgumentException('RGB values must be between 0 and 255.');
            }
        }
        return $color;
    }
}
