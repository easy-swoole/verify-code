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
    private string $captchaByte;  // 验证码图片
    private string $captchaMime;  // 验证码类型
    private string $captchaCode;  // 验证码内容
    private int $createTime;
    private ?CaptchaColor $targetColor;

    public function __construct(string $byte, string $code, string $mime, ?CaptchaColor $targetColor = null)
    {
        $this->captchaByte = $byte;
        $this->captchaMime = $mime;
        $this->captchaCode = $code;
        $this->targetColor = $targetColor;
        $this->createTime = time();
    }

    public function getCreateTime(): int
    {
        return $this->createTime;
    }

    public function getCodeHash(?string $code = null, ?int $time = null): string
    {
        $code ??= $this->captchaCode;
        $time ??= $this->createTime;
        return substr(md5($code . $time), 8, 16);
    }

    /**
     * 获取验证码图片
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    public function getImageByte(): string
    {
        return $this->captchaByte;
    }

    /**
     * 返回图片Base64字符串
     * @author : evalor <master@evalor.cn>
     * @return string
     */
    public function getImageBase64(): string
    {
        $base64Data = base64_encode($this->captchaByte);
        return "data:{$this->captchaMime};base64,{$base64Data}";
    }

    /**
     * 获取验证码内容
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    public function getImageCode(): string
    {
        return $this->captchaCode;
    }

    /**
     * 获取Mime信息
     * @author : evalor <master@evalor.cn>
     */
    public function getImageMime(): string
    {
        return $this->captchaMime;
    }

    public function getTargetColor(): ?CaptchaColor
    {
        return $this->targetColor;
    }

    public function getPrompt(): ?string
    {
        return $this->targetColor === null
            ? null
            : "请输入{$this->targetColor->label()}的验证码";
    }
}
