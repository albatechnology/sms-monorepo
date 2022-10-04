<?php

namespace App\Enums;

/**
 * @method static static SINGLE()
 * @method static static MULTIPLE()
 */
final class TargetChartType extends BaseEnum
{
    public const SINGLE   = "single";
    public const MULTIPLE = "multiple";

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}