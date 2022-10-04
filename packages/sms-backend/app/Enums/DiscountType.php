<?php

namespace App\Enums;

/**
 * @method static static NOMINAL()
 * @method static static PERCENTAGE()
 */
final class DiscountType extends BaseEnum
{
    const NOMINAL    = 0;
    const PERCENTAGE = 1;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}