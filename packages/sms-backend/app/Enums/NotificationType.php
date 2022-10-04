<?php

namespace App\Enums;

use App\Notifications\ActivityReminder;
use App\Notifications\DiscountApproval;
use App\Notifications\NewLeadAssigned;

/**
 * @method static static ActivityReminder()
 */
final class NotificationType extends BaseEnum
{
    public const ActivityReminder = ActivityReminder::class;
    public const NewLeadAssigned = NewLeadAssigned::class;
    public const DiscountApproval = DiscountApproval::class;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}
