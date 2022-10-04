<?php

namespace App\Enums\Import;

use App\Enums\BaseEnum;

/**
 * @method static static PREVIEW()
 * @method static static CREATED()
 * @method static static UPDATED()
 * @method static static SKIPPED()
 * @method static static ERROR()
 */
final class ImportLineStatus extends BaseEnum
{
    const PREVIEW = 0;
    const CREATED = 1;
    const UPDATED = 2;
    const SKIPPED = 3;
    const ERROR   = 4;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}