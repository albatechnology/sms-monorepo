<?php

namespace App\Enums;

/**
 * @method static static PHONE()
 * @method static static WHATSAPP()
 * @method static static MEETING()
 * @method static static OTHERS()
 * @method static static WALK_IN_CUSTOMER()
 * @method static static NEW_ORDER()
 */
final class ActivityFollowUpMethod extends BaseEnum
{
    const PHONE            = 1;
    const WHATSAPP         = 2;
    const MEETING          = 3;
    const OTHERS           = 4;
    const WALK_IN_CUSTOMER = 5;
    const NEW_ORDER        = 6;

    public static function getDescription($value): string
    {
        return match ($value) {
            self::WALK_IN_CUSTOMER => 'Walk in Customer',
            self::NEW_ORDER => 'New order',
            default => ucfirst(strtolower(self::getKey($value))),
        };
    }

    public static function readOnly(): array
    {
        return [
            self::NEW_ORDER()
        ];
    }
}