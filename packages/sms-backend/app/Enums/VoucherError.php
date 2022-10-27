<?php

namespace App\Enums;

/**
 * @method static static INACTIVE()
 * @method static static UNDER_MINIMUM_PRICE()
 */
final class VoucherError extends BaseEnum
{
    const INACTIVE                      = "VI01";
    // const USE_LIMIT_REACHED             = "V102";
    const UNDER_MINIMUM_PRICE           = "V103";
    // const NOT_APPLICABLE_TO_ANY_PRODUCT = "V104";

    public static function getDescription($value): string
    {
        return match ($value) {
            self::INACTIVE => 'This voucher is no longer active.',
            // self::USE_LIMIT_REACHED => 'This customer has reached the voucher use limit.',
            self::UNDER_MINIMUM_PRICE => 'The order price is under the minimum required price for the voucher.',
            // self::NOT_APPLICABLE_TO_ANY_PRODUCT => 'The selected voucher does not apply to any of the products.',
            default => self::getKey($value),
        };
    }

    public static function valueType()
    {
        return 'string';
    }
}
