<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/11/13
 * Time: 下午10:57
 */

namespace easyswoole\VerifyCode;


class Result
{
    private $imageBody;
    private $imageStr;
    function __construct($image,$str)
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

    function getMineType(){
        return 'image/png';
    }
}