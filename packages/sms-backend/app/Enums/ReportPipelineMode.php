<?php

namespace App\Enums;

/**
 * @method static static ADD_TARGET_MAP()
 * @method static static EVALUATE_TARGET()
 * @method static static ADD_NEW_TARGET_MAP_TO_TARGET()
 */
final class ReportPipelineMode extends BaseEnum
{
    public const ADD_TARGET_MAP               = 'add_target_map';
    public const EVALUATE_TARGET              = 'evaluate_target';
    public const ADD_NEW_TARGET_MAP_TO_TARGET = 'add_new_target_map_to_target';

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}