<?php

namespace App\Enums;

/**
 * @method static static NOT_REQUIRED()
 * @method static static WAITING_APPROVAL()
 * @method static static APPROVED()
 */
final class OrderApprovalStatus extends BaseEnum
{
    public const NOT_REQUIRED     = 0;
    public const WAITING_APPROVAL = 1;
    public const APPROVED         = 2;
    public const REJECTED         = 3;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}
