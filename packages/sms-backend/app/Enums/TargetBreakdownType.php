<?php

namespace App\Enums;

/**
 * @method static static ACTIVITY_STATUS()
 */
final class TargetBreakdownType extends BaseEnum
{
    public const ACTIVITY_STATUS = 1;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}