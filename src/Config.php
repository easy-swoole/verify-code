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

/**
 * 验证码配置文件
 * Class VerifyCodeConf
 * @author  : evalor <master@evalor.cn>
 * @package Vendor\VerifyCode
 */
class Config extends SplBean
{

    public $charset   = '1234567890AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz'; // 字母表
    public $useCurve  = false;         // 混淆曲线
    public $useNoise  = true;         // 随机噪点
    public $useFont   = null;          // 指定字体
    public $fontColor = null;          // 字体颜色
    public $backColor = null;          // 背景颜色
    public $imageL    = null;          // 图片宽度
    public $imageH    = null;          // 图片高度
    public $fonts     = [];            // 额外字体
    public $fontSize  = 25;            // 字体大小
    public $mime      = MIME::PNG;     // 设置类型
    public $temp      = '/tmp';  // 设置缓存目录

    public function setTemp($temp){
        if (!is_dir($temp)) mkdir($temp,0755) && chmod($temp,0755);
        $this->temp = $temp;
    }

    /**
     * 设置图片格式
     * @param $MimeType
     * @return Config
     *@author : evalor <master@evalor.cn>
     */
    public function setMimeType($MimeType)
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
    public function setCharset($charset)
    {
        is_string($charset) && $this->charset = $charset;
        return $this;
    }

    /**
     * 开启混淆曲线
     * @param bool $useCurve
     * @return Config
     */
    public function setUseCurve($useCurve = true)
    {
        is_bool($useCurve) && $this->useCurve = $useCurve;
        return $this;
    }

    /**
     * 开启噪点生成
     * @param bool $useNoise
     * @return Config
     */
    public function setUseNoise($useNoise = true)
    {
        is_bool($useNoise) && $this->useNoise = $useNoise;
        return $this;
    }

    /**
     * 使用自定义字体
     * @param string $useFont
     * @return Config
     */
    public function setUseFont($useFont)
    {
        is_string($useFont) && $this->useFont = $useFont;
        return $this;
    }

    /**
     * 设置文字颜色
     * @param array|string $fontColor
     * @return Config
     */
    public function setFontColor($fontColor)
    {
        if (is_string($fontColor)) $this->fontColor = $this->HEXToRGB($fontColor);
        if (is_array($fontColor)) $this->fontColor = $fontColor;
        return $this;
    }

    /**
     * 设置背景颜色
     * @param null $backColor
     * @return Config
     */
    public function setBackColor($backColor)
    {
        if (is_string($backColor)) $this->backColor = $this->HEXToRGB($backColor);
        if (is_array($backColor)) $this->backColor = $backColor;
        return $this;
    }

    /**
     * 设置图片宽度
     * @param int|string $imageL
     * @return Config
     */
    public function setImageWidth($imageL)
    {
        $this->imageL = intval($imageL);
        return $this;
    }

    /**
     * 设置图片高度
     * @param null $imageH
     * @return Config
     */
    public function setImageHeight($imageH)
    {
        $this->imageH = intval($imageH);
        return $this;
    }

    /**
     * 设置字体集
     * @param array|string $fonts
     * @return Config
     */
    public function addFonts($fonts)
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
    public function setFontSize($fontSize)
    {
        $this->fontSize = intval($fontSize);
        return $this;
    }


    /**
     * 十六进制转RGB
     * @param $hexColor
     * @author : evalor <master@evalor.cn>
     * @return array
     */
    function HEXToRGB($hexColor)
    {
        $color = str_replace('#', '', $hexColor);
        if (strlen($color) > 3) {
            $rgb = array(
                hexdec(substr($color, 0, 2)),
                hexdec(substr($color, 2, 2)),
                hexdec(substr($color, 4, 2))
            );
        } else {
            $color = $hexColor;
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
}