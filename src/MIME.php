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
 * 验证码MIME类型
 * Class MIME
 * @author : evalor <master@evalor.cn>
 * @package easySwoole\VerifyCode
 */
class MIME
{
    public const JPG = 'image/jpeg';
    public const PNG = 'image/png';
    public const GIF = 'image/gif';

    /**
     * 获取对应Mime类型的后缀名称
     * @author : evalor <master@evalor.cn>
     * @param $Mime
     * @return mixed
     */
    public static function getExtensionName(string $mime): string
    {
        return match ($mime) {
            self::JPG => 'jpeg',
            self::PNG => 'png',
            self::GIF => 'gif',
            default => throw new \InvalidArgumentException("Unsupported MIME type: {$mime}"),
        };
    }
}
