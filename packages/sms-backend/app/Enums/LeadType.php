<?php

namespace App\Enums;

/**
 * @method static static PROSPECT()
 * @method static static CLOSED()
 * @method static static LEADS()
 * @method static static DROP()
 */
final class LeadType extends BaseEnum
{
    const PROSPECT = 1;
    const DEAL = 2;
    const LEADS = 3;
    const DROP = 4;
    const CLOSED = 5;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}
