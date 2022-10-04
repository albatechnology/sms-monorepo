<?php

namespace App\Enums;

/**
 * @method static static PREPARING()
 * @method static static DELIVERING()
 * @method static static ARRIVED()
 * @method static static CANCELLED()
 */
final class ShipmentStatus extends BaseEnum
{
    public const PREPARING  = 0;
    public const DELIVERING = 1;
    public const ARRIVED    = 2;
    public const CANCELLED  = 3;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}
