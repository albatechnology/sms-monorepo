<?php

namespace App\Enums\Import;

use App\Enums\BaseEnum;

/**
 * @method static static ERROR_PRE_LOAD()
 * @method static static READY_TO_IMPORT_LINES()
 * @method static static GENERATING_PREVIEW()
 * @method static static ERROR_PROCESSING()
 * @method static static PREVIEW()
 * @method static static IMPORTING()
 * @method static static FINISHED()
 * @method static static CANCELLED()
 */
final class ImportBatchStatus extends BaseEnum
{
    const ERROR_PRE_LOAD        = 1; // We know file is problematic even before we start reading row (i.e., missing header)
    const READY_TO_IMPORT_LINES = 2; // Passes preliminary check
    const GENERATING_PREVIEW    = 3; // Currently importing to order lines, generating preview
    const ERROR_PROCESSING      = 4; // Found problem while importing import line, could not import all line.
    const PREVIEW               = 5; // Finished validating all import lines and generate report preview
    const IMPORTING             = 7; // Importing import lines to actual model
    const FINISHED              = 8; // Finished importing to models
    const CANCELLED             = 9;

    public static function getDescription($value): string
    {
        return match ($value) {
            self::READY_TO_IMPORT_LINES => 'READY',
            default => self::getKey($value),
        };
    }

    public function getColourSchema()
    {
        return match ($this->value) {
            self::ERROR_PRE_LOAD, self::ERROR_PROCESSING => 'danger',
            self::READY_TO_IMPORT_LINES, self::PREVIEW => 'primary',
            self::GENERATING_PREVIEW, self::IMPORTING => 'info',
            self::FINISHED => 'success',
            default => self::getKey('secondary'),
        };
    }

    public function cancellable(): bool
    {
        return $this->in([
            self::PREVIEW
        ]);
    }

    public function isLoading(): bool
    {
        return $this->in([
            self::GENERATING_PREVIEW, self::IMPORTING
        ]);
    }

    public function isGeneratingPreview(): bool
    {
        return $this->is(self::GENERATING_PREVIEW);
    }

    public function isImporting(): bool
    {
        return $this->is(self::IMPORTING);
    }
}