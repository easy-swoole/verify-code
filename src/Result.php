<?php
// +----------------------------------------------------------------------
// | easySwoole [ use swoole easily just like echo "hello world" ]
// +----------------------------------------------------------------------
// | WebSite: https://www.easyswoole.com
// +----------------------------------------------------------------------
// | Welcome Join QQGroup 633921431
// +----------------------------------------------------------------------

namespace EasySwoole\VerifyCode;

/**
 * 验证码结果类
 * Class Result
 * @author : evalor <master@evalor.cn>
 * @package easySwoole\VerifyCode
 */
class Result
{
    private $captchaByte;  // 验证码图片
    private $captchaMime;  // 验证码类型
    private $captchaCode;  // 验证码内容
    private $createTime;

    function __construct($Byte, $Code, $Mime)
    {
        $this->captchaByte = $Byte;
        $this->captchaMime = $Mime;
        $this->captchaCode = $Code;
        $this->createTime = time();
    }

    function getCreateTime():int
    {
        return $this->createTime;
    }

    function getCodeHash($code = null,$time = null)
    {
        if(!$code){
            $code = $this->captchaCode;
        }
        if(!$time){
            $time = $this->createTime;
        }
        return substr(md5($code.$time),8,16);
    }

    /**
     * 获取验证码图片
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    function getImageByte()
    {
        return $this->captchaByte;
    }

    /**
     * 返回图片Base64字符串
     * @author : evalor <master@evalor.cn>
     * @return string
     */
    function getImageBase64()
    {
        $base64Data = base64_encode($this->captchaByte);
        $Mime = $this->captchaMime;
        return "data:{$Mime};base64,{$base64Data}";
    }

    /**
     * 获取验证码内容
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    function getImageCode()
    {
        return $this->captchaCode;
    }

    /**
     * 获取Mime信息
     * @author : evalor <master@evalor.cn>
     */
    function getImageMime()
    {
        return $this->captchaMime;
    }
}