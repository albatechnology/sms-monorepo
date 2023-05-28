<?php

namespace App\Enums;

/**
 * @method static static QUANTITY()
 * @method static static TYPE()
 * @method static static TRANSACTION()
 * @method static static CATEGORY()
 * @method static static SECOND_PLACE_BRAND_PRICE()
 */
final class DiscountScope extends BaseEnum
{
    const QUANTITY                  = 0;
    const TYPE                      = 1;
    const TRANSACTION               = 2;
    // const CATEGORY                  = 3;
    // const SECOND_PLACE_BRAND_PRICE  = 4;
    // const PACKAGE                   = 5;

    public static function getDescription($value): string
    {
        return match ($value) {
            self::QUANTITY                  => 'Per Product Unit Quantity',
            self::TYPE                      => 'Per Product Unit Type',
            self::TRANSACTION               => 'Per Transaction',
            // self::CATEGORY                  => 'Per Product Unit Category',
                // self::SECOND_PLACE_BRAND_PRICE  => 'Per Second Place of Brand Price ',
                // self::PACKAGE                   => 'Per Required Product Unit of Package',
            default => self::getKey($value),
        };
    }

    public function applyToOrder(): bool
    {
        return match ($this->value) {
            self::TRANSACTION => true,
            default => false,
        };
    }
}
