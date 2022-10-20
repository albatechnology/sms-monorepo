<?php

namespace App\Enums;

/**
 * @method static static INDENT_PREMIUM()
 * @method static static INDENT_REGULAR()
 * @method static static READY_PREMIUM()
 * @method static static READY_REGULAR()
 * @method static static INCOMING_CONTAINER()
 * @method static static PRODUCT_HIGHLIGHT()
 */
final class ProductCategory extends BaseEnum
{
    const INDENT_PREMIUM        = 0;
    const INDENT_REGULAR        = 1;
    const READY_PREMIUM         = 2;
    const READY_REGULAR         = 3;
    const INCOMING_CONTAINER    = 4;
    const PRODUCT_HIGHLIGHT     = 5;

    public static function getDescription($value): string
    {
        return match ($value) {
            self::INDENT_PREMIUM        => 'Indent Premium',
            self::INDENT_REGULAR        => 'Indent Regular',
            self::READY_PREMIUM         => 'Ready Premium',
            self::READY_REGULAR         => 'Ready Regular',
            self::INCOMING_CONTAINER    => 'Incoming Container',
            self::PRODUCT_HIGHLIGHT     => 'Product Highlight',
            default => self::getKey($value),
        };
    }
}
