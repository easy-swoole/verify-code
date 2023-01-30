<?php

namespace EasySwoole\VerifyCode;

/**
 * 验证码处理类
 */
class VerifyCode
{
    protected Config $conf;
    protected $imInstance;

    protected mixed $useFont;

    public function __construct(Config $option = null)
    {
        if($option == null){
            $this->conf = new Config();
        }else{
            $this->conf = $option;
        }

        if($this->conf->useFont){
            $this->useFont = $this->conf->useFont;
        }else{
            $assetsPath = __DIR__ . '/assets/';
            $fonts = $this->loadFonts($assetsPath . 'ttf/');
            $this->conf->addFonts($fonts);
            $fonts = $this->conf->fonts;
            $this->useFont = $fonts[array_rand($fonts)];
        }

        $this->conf->fontColor || $this->conf->fontColor = [mt_rand(1, 150), mt_rand(1, 150), mt_rand(1, 150)];
        $this->conf->backColor || $this->conf->backColor = [255, 255, 255];
    }


    function drawCode(string $code): Result
    {
        $codeLen = strlen($code);
        $this->conf->imageL = $codeLen * $this->conf->fontSize * 1.5 + $this->conf->fontSize / 2;
        $this->conf->imageH = $this->conf->fontSize * 2;



        // 创建空白画布
        $this->imInstance = imagecreate((int)$this->conf->imageL, (int)$this->conf->imageH);
        // 设置背景颜色
        $this->conf->backColor = imagecolorallocate($this->imInstance, $this->conf->backColor[0], $this->conf->backColor[1], $this->conf->backColor[2]);
        // 设置字体颜色
        $this->conf->fontColor = imagecolorallocate($this->imInstance, $this->conf->fontColor[0], $this->conf->fontColor[1], $this->conf->fontColor[2]);
        // 画干扰噪点
        if ($this->conf->useNoise) $this->writeNoise();
        // 画干扰曲线
        if ($this->conf->useCurve) $this->writeCurve();

        // 绘验证码
        $codeNX = 0; // 验证码第N个字符的左边距
        for ($i = 0; $i < $codeLen; $i++) {
            $codeNX += mt_rand($this->conf->fontSize * 1.2, $this->conf->fontSize * 1.4);
            // 写一个验证码字符
            imagettftext($this->imInstance, $this->conf->fontSize, mt_rand(-50, 50), $codeNX, (int)($this->conf->fontSize * 1.5), $this->conf->fontColor, $this->useFont, $code[$i]);
        }

        // 输出验证码结果集
        $this->conf->temp = rtrim(str_replace('\\', '/', $this->conf->temp), '/') . '/';
        mt_srand();
        $func = 'image' . MIME::getExtensionName($this->conf->mime);
        ob_start();
        $func($this->imInstance);
        $file = ob_get_contents();
        ob_end_clean();
        imagedestroy($this->imInstance);
        return new Result($file, $code, $this->conf->mime);
    }


    private function loadFonts($fontsPath): array
    {
        $dir = dir($fontsPath);
        $fonts = [];
        while (false !== ($file = $dir->read())) {
            if ('.' != $file[0] && substr($file, -4) == '.ttf') {
                $fonts[] = $fontsPath . $file;
            }
        }
        $dir->close();
        return $fonts;
    }

    /**
     * 画干扰杂点
     * @author : evalor <master@evalor.cn>
     */
    private function writeNoise(): void
    {
        $codeSet = '2345678abcdefhijkmnpqrstuvwxyz';
        for ($i = 0; $i < 10; $i++) {
            $noiseColor = imagecolorallocate($this->imInstance, mt_rand(150, 225), mt_rand(150, 225), mt_rand(150, 225));
            for ($j = 0; $j < 5; $j++) {
                // 绘杂点
                imagestring($this->imInstance, 5, mt_rand(-10, (int)$this->conf->imageL), mt_rand(-10, (int)$this->conf->imageH), $codeSet[mt_rand(0, 29)], $noiseColor);
            }
        }
    }

    /**
     * 画干扰曲线
     * @author : evalor <master@evalor.cn>
     */
    private function writeCurve(): void
    {
        $px = $py = 0;
        // 曲线前部分
        $A = mt_rand(1, $this->conf->imageH / 2); // 振幅
        $b = mt_rand(-$this->conf->imageH / 4, $this->conf->imageH / 4); // Y轴方向偏移量
        $f = mt_rand(-$this->conf->imageH / 4, $this->conf->imageH / 4); // X轴方向偏移量
        $T = mt_rand($this->conf->imageH, $this->conf->imageL * 2); // 周期
        $w = (2 * M_PI) / $T;
        $px1 = 0; // 曲线横坐标起始位置
        $px2 = mt_rand($this->conf->imageL / 2, $this->conf->imageL * 0.8); // 曲线横坐标结束位置
        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $this->conf->imageH / 2; // y = Asin(ωx+φ) + b
                $i = (int)($this->conf->fontSize / 5);
                while ($i > 0) {
                    // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多
                    imagesetpixel($this->imInstance, $px + $i, $py + $i, $this->conf->fontColor);
                    $i--;
                }
            }
        }
        // 曲线后部分
        $A = mt_rand(1, $this->conf->imageH / 2); // 振幅
        $f = mt_rand(-$this->conf->imageH / 4, $this->conf->imageH / 4); // X轴方向偏移量
        $T = mt_rand($this->conf->imageH, $this->conf->imageL * 2); // 周期
        $w = (2 * M_PI) / $T;
        $b = $py - $A * sin($w * $px + $f) - $this->conf->imageH / 2;
        $px1 = $px2;
        $px2 = $this->conf->imageL;
        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $this->conf->imageH / 2; // y = Asin(ωx+φ) + b
                $i = (int)($this->conf->fontSize / 5);
                while ($i > 0) {
                    imagesetpixel($this->imInstance, $px + $i, $py + $i, $this->conf->fontColor);
                    $i--;
                }
            }
        }
    }
}