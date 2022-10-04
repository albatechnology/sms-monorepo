<?php

namespace App\Enums;

/**
 * @method static static MANUAL()
 * @method static static ORDER()
 * @method static static TRANSFER()
 */
final class StockHistoryType extends BaseEnum
{
    const MANUAL   = 0;
    const ORDER    = 1;
    const TRANSFER = 2;

    public static function getDescription($value): string
    {
        return match ($value) {
            self::MANUAL => 'Stock has been modified manually via CMS',
            self::ORDER => 'Stock has been modified to fulfil an order',
            self::TRANSFER => 'Stock has been transferred from one channel to another',
            default => self::getKey($value),
        };
    }
}