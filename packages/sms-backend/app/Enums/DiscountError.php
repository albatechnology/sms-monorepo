<?php

namespace App\Enums;

/**
 * @method static static INACTIVE()
 * @method static static USE_LIMIT_REACHED()
 * @method static static UNDER_MINIMUM_PRICE()
 * @method static static NOT_APPLICABLE_TO_ANY_PRODUCT()
 */
final class DiscountError extends BaseEnum
{
    const INACTIVE                      = "DI01";
    const USE_LIMIT_REACHED             = "D102";
    const UNDER_MINIMUM_PRICE           = "D103";
    const NOT_APPLICABLE_TO_ANY_PRODUCT = "D104";

    public static function getDescription($value): string
    {
        return match ($value) {
            self::INACTIVE => 'This discount is no longer active.',
            self::USE_LIMIT_REACHED => 'This customer has reached the discount use limit.',
            self::UNDER_MINIMUM_PRICE => 'The order price is under the minimum required price for the discount.',
            self::NOT_APPLICABLE_TO_ANY_PRODUCT => 'The selected discount does not apply to any of the products.',
            default => self::getKey($value),
        };
    }

    public static function valueType()
    {
        return 'string';
    }
}