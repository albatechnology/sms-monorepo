<?php

namespace App\Enums;

/**
 * @method static static PENDING()
 * @method static static APPROVED()
 * @method static static REJECTED()
 */
final class PaymentStatus extends BaseEnum
{
    const PENDING  = 0;
    const APPROVED = 1;
    const REJECTED = 2;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}