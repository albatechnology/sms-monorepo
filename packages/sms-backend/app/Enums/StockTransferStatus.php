<?php

namespace App\Enums;

/**
 * @method static static PENDING()
 * @method static static FAILED()
 * @method static static COMPLETE()
 */
final class StockTransferStatus extends BaseEnum
{
    const PENDING  = 0;
    const FAILED   = 1;
    const COMPLETE = 2;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}