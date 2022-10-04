<?php

namespace App\Enums;

use App\Interfaces\InTargetBreakdown;

/**
 * @method static static HOT()
 * @method static static WARM()
 * @method static static COLD()
 * @method static static CLOSED()
 */
final class ActivityStatus extends BaseEnum implements InTargetBreakdown
{
    const HOT    = 1;
    const WARM   = 2;
    const COLD   = 3;
    const CLOSED = 4;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}
