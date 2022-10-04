<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CurrencyList extends Enum
{
    const IDR = 'IDR';
    const USD = 'USD';
    const EUR = 'EUR';
    const JPY = 'JPY';
    const MYR = 'MYR';
    const SGD = 'SGD';
    const CNY = 'CNY';
    const HKD = 'HKD';
    const MMK = 'MMK';
    const SAR = 'SAR';
    const KRW = 'KRW';
    const GBP = 'GBP';
    const CHF = 'CHF';
    const CAD = 'CAD';
    const AUD = 'AUD';

    public static function getDescription($value): string
    {
        return match ($value) {
            self::IDR => '(IDR) Indonesia',
            self::USD => '(USD) U.S. Dollar',
            self::EUR => '(EUR) European Euro',
            self::JPY => '(JPY) Japanese Yen',
            self::MYR => '(MYR) Ringgit Malaysia',
            self::SGD => '(SGD) Dollar Singapore',
            self::CNY => '(CNY) China Yuan',
            self::HKD => '(HKD) Hong Kong Dollar',
            self::MMK => '(MMK) Myanmar Kyat',
            self::SAR => '(SAR) Saudi Arabia Rial',
            self::KRW => '(KRW) South Korean Won',
            self::GBP => '(GBP) British Pound',
            self::CHF => '(CHF) Swiss Franc',
            self::CAD => '(CAD) Canadian Dollar',
            self::AUD => '(AUD) Australian/New Zealand Dollar',
            default => self::getKey($value),
        };
    }
}
