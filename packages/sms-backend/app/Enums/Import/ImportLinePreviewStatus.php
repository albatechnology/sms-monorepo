<?php

namespace App\Enums\Import;

use App\Enums\BaseEnum;

/**
 * @method static static NONE()
 * @method static static ERROR()
 * @method static static NEW()
 * @method static static DUPLICATE()
 */
final class ImportLinePreviewStatus extends BaseEnum
{
    const NONE      = 0;
    const ERROR     = 1;
    const NEW       = 2;
    const DUPLICATE = 3;

    public array $errors = [];

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}