<?php

namespace EasySwoole\VerifyCode;

enum CaptchaColor: string
{
    case RED = 'red';
    case BLUE = 'blue';
    case GREEN = 'green';
    case PURPLE = 'purple';
    case ORANGE = 'orange';
    case BLACK = 'black';

    public function label(): string
    {
        return match ($this) {
            self::RED => '红色',
            self::BLUE => '蓝色',
            self::GREEN => '绿色',
            self::PURPLE => '紫色',
            self::ORANGE => '橙色',
            self::BLACK => '黑色',
        };
    }

    /** @return array{int, int, int} */
    public function rgb(): array
    {
        return match ($this) {
            self::RED => [196, 45, 55],
            self::BLUE => [36, 87, 166],
            self::GREEN => [28, 128, 74],
            self::PURPLE => [122, 46, 142],
            self::ORANGE => [210, 105, 30],
            self::BLACK => [35, 35, 35],
        };
    }

    /**
     * 返回与目标色区分度较高的干扰色，避开红/绿、红/橙、蓝/紫等组合。
     * 数量不足时应复用这些安全颜色，而不是回退到相近颜色。
     *
     * @return list<self>
     */
    public function safeDecoyColors(): array
    {
        return match ($this) {
            self::RED => [self::BLUE, self::BLACK],
            self::BLUE => [self::ORANGE, self::RED],
            self::GREEN => [self::PURPLE, self::BLACK],
            self::PURPLE => [self::ORANGE, self::GREEN],
            self::ORANGE => [self::BLUE, self::BLACK],
            self::BLACK => [self::ORANGE, self::RED, self::GREEN],
        };
    }
}
