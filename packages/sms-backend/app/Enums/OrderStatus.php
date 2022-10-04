<?php

namespace App\Enums;

/**
 * @method static static QUOTATION()
 * @method static static SHIPMENT()
 * @method static static CANCELLED()
 * @method static static RETURNED()
 */
final class OrderStatus extends BaseEnum
{
    const QUOTATION = 1;
    const SHIPMENT  = 2;
    //const DELIVERING = 3;
    //const ARRIVED    = 4;
    const CANCELLED = 5;
    const RETURNED  = 6;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}
