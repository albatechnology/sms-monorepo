<?php

namespace App\Enums;

/**
 * @method static static MR()
 * @method static static MS()
 * @method static static MRS()
 */
final class PersonTitle extends BaseEnum
{
    const MR  = 1;
    const MS  = 2;
    const MRS = 3;
    const PT = 4;

    public static function getDescription($value): string
    {
        return match ($value) {
            self::MR => "Mr.",
            self::MS => "Ms.",
            self::MRS => "Mrs.",
            self::PT => "PT.",
            default => self::getKey($value),
        };
    }
}
