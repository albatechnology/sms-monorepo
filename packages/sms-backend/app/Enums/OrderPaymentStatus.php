<?php

namespace App\Enums;

/**
 * @method static static NONE()
 * @method static static PARTIAL()
 * @method static static SETTLEMENT()
 * @method static static OVERPAYMENT()
 * @method static static REFUNDED()
 * @method static static DOWN_PAYMENT()
 */
final class OrderPaymentStatus extends BaseEnum
{
    const NONE         = 1;
    const PARTIAL      = 2;
    const SETTLEMENT   = 3;
    const OVERPAYMENT  = 4;
    const REFUNDED     = 5;
    const DOWN_PAYMENT = 6;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}
