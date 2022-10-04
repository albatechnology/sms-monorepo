<?php

namespace App\Enums;

/**
 * @method static static INDENT()
 * @method static static FULFILLED()
 */
final class OrderStockStatus extends BaseEnum
{
    const INDENT    = 0;
    const FULFILLED = 1;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}