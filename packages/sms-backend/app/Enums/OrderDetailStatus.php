<?php

namespace App\Enums;

/**
 * @method static static NOT_FULFILLED()
 * @method static static PARTIALLY_FULFILLED()
 * @method static static FULFILLED()
 * @method static static OVER_FULFILLED()
 */
final class OrderDetailStatus extends BaseEnum
{
    public const NOT_FULFILLED       = 1;
    public const PARTIALLY_FULFILLED = 2;
    public const FULFILLED           = 3;
    public const OVER_FULFILLED      = 4;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}