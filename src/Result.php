<?php
// +----------------------------------------------------------------------
// | easySwoole [ use swoole easily just like echo "hello world" ]
// +----------------------------------------------------------------------
// | WebSite: https://www.easyswoole.com
// +----------------------------------------------------------------------
// | Welcome Join QQGroup 633921431
// +----------------------------------------------------------------------

namespace easySwoole\VerifyCode;

class Result
{
    private $imageBody;
    private $imageStr;

    function __construct($image, $str)
    {
        $this->imageBody = $image;
        $this->imageStr = $str;
    }

    /**
     * @return mixed
     */
    public function getImageBody()
    {
        return $this->imageBody;
    }

    /**
     * @return mixed
     */
    public function getImageStr()
    {
        return $this->imageStr;
    }

    function getMineType()
    {
        return 'image/png';
    }
}