<?php

namespace App\Enums;

/**
 * @method static static OUTBOUND()
 * @method static static INBOUND()
 */
final class StockHistoryDirection extends BaseEnum
{
    const OUTBOUND = 0;
    const INBOUND  = 1;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}