<?php

namespace App\Enums;

/**
 * @method static static NONE()
 * @method static static PARTIAL()
 * @method static static PREPARING()
 * @method static static DELIVERING()
 * @method static static ARRIVED()
 */
final class OrderShipmentStatus extends BaseEnum
{
    public const NONE       = 0;
    public const PARTIAL    = 1;
    public const PREPARING  = 2;
    public const DELIVERING = 3;
    public const ARRIVED    = 4;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}
