<?php

namespace App\Classes\DocGenerator\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static DEFAULT()
 * @method static static NUMERIC()
 * @method static static BOOLEAN()
 * @method static static DATE()
 * @method static static CSV()
 */
final class DataFormat extends Enum
{
    const DEFAULT = "default";
    const NUMERIC = "numeric";
    const BOOLEAN = "boolean";
    const DATE    = "date";
    const CSV     = "csv";
    const ENUM    = "enum";

    public static function getDescription($value): string
    {
        return match ($value) {
            self::DEFAULT => 'Any string',
            self::NUMERIC => 'Numeric (numbers) only',
            self::BOOLEAN => 'Accept true, false, 0, and 1',
            self::DATE => 'Date in Y-m-d format unless stated otherwise on validationRule',
            self::CSV => 'List of values formatted as comma separated string',
            self::ENUM => 'Accept from a list of pre-defined values. Uppercase, snake case convention is used.',
            default => self::getKey($value),
        };
    }
}
