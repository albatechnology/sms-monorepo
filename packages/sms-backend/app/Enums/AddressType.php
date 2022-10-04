<?php

namespace App\Enums;

/**
 * @method static static ADDRESS()
 * @method static static DELIVERY()
 * @method static static BILLING()
 */
final class AddressType extends BaseEnum
{
    const ADDRESS  = 1;
    const DELIVERY = 2;
    const BILLING  = 3;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}
