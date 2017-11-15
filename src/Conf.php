<?php
// +----------------------------------------------------------------------
// | easySwoole [ use swoole easily just like echo "hello world" ]
// +----------------------------------------------------------------------
// | WebSite: https://www.easyswoole.com
// +----------------------------------------------------------------------
// | Welcome Join QQGroup 633921431
// +----------------------------------------------------------------------

namespace easySwoole\VerifyCode;

use Core\Component\Spl\SplBean;

/**
 * 验证码配置文件
 * Class VerifyCodeConf
 * @author : evalor <master@evalor.cn>
 * @package Vendor\VerifyCode
 */
class Conf extends SplBean
{

    public $charset   = '1234567890AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz'; // 字母表
    public $useCurve  = false; // 混淆曲线
    public $useNoise  = false; // 随机噪点
    public $useFont   = null;  // 指定字体
    public $fontColor = null;  // 字体颜色
    public $imageL    = null;  // 图片宽度
    public $imageH    = null;  // 图片高度
    public $fonts     = [];    // 额外字体
    public $fontSize  = 25;    // 字体大小
    public $length    = 4;     // 生成位数

    protected function initialize()
    {
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}