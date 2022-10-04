<?php

namespace App\Imports;

use App\Enums\Import\ImportLinePreviewStatus;
use App\Enums\Import\ImportLineStatus;
use App\Enums\Import\ImportMode;
use App\Exceptions\InsufficientStockException;
use App\Models\ImportLine;
use App\Models\Stock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockImport extends BaseImport implements ToModel, WithHeadingRow
{
    public static function getModelName(): string
    {
        return Stock::class;
    }

    /**
     * May provide export sample data
     * @return Collection
     */
    public static function getExportCollection(): Collection
    {
        return Collect([
            self::getHeader(),
            [1, 1,],
            [2, -1]
        ]);
    }

    public static function getHeader(): array
    {
        return [
            'id', 'stock'
        ];
    }

    /**
     * Override validate line rule, default status to duplicate (since
     * we ever only allow for stock update)
     * @param ImportLine $line
     */
    public function validateLine(ImportLine $line)
    {
        $line->errors         = [];
        $line->preview_status = ImportLinePreviewStatus::DUPLICATE();

        $validator = Validator::make($line->data, $this->getValidationRule($line->data));
        $line->addErrors($validator->errors()->all());

        if ($line->hasError()) {
            $line->preview_status = ImportLinePreviewStatus::ERROR();
        }
    }

    protected function getValidationRule(array $data): array
    {
        return [
            'id'    => [
                'required',
                Rule::exists('stocks', 'id')
                    ->where('company_id', $this->batch->company_id)
            ],
            'stock' => [
                'required', 'integer',
            ],
        ];
    }

    /**
     * Update existing model based on import line.
     * TODO: we will need some check to prevent going to negative.
     *    most probably a post update check with rollback
     *
     * @param ImportLine $line
     * @return mixed
     * @throws InsufficientStockException
     */
    protected function updateModel(ImportLine $line): void
    {
        /** @var Stock $stock */
        Stock::query()
            ->where('id', $line->data['id'])
            ->increment('stock', $line->data['stock']);
    }

    protected function updateArray(ImportLine $line): array
    {
        return [];
    }

    /**
     * Create model not allowed for stock. Either disable,
     * or treat as update.
     * @param ImportLine $line
     * @return mixed
     */
    protected function createModel(ImportLine $line): void
    {
    }

    protected function createArray(ImportLine $line): array
    {
        return [];
    }

    /**
     * Process the import line to model.
     * This will be called from ProcessImportLineJob
     * @param ImportLine $line
     */
    public function process(ImportLine $line)
    {
        if ($line->status->isNot(ImportLineStatus::PREVIEW)) return;

        // re-validate each line
        $this->validateLine($line);

        // error, always skip
        if ($line->preview_status->is(ImportLinePreviewStatus::ERROR)) {
            $line->updateStatus(ImportLineStatus::SKIPPED());
            return;
        }

        // duplicate
        if ($line->preview_status->is(ImportLinePreviewStatus::DUPLICATE)) {

            // skip duplicate
            if ($this->batch->mode->is(ImportMode::SKIP_DUPLICATE)) {
                $line->updateStatus(ImportLineStatus::SKIPPED());
                return;
            }

            // Update duplicate
            if ($this->batch->mode->is(ImportMode::UPDATE_DUPLICATE)) {
                $this->updateModel($line);
                $line->updateStatus(ImportLineStatus::UPDATED());
                return;
            }
        }

        // new data, should not happen here
        if ($line->preview_status->is(ImportLinePreviewStatus::NEW)) {
            $line->updateStatus(ImportLineStatus::SKIPPED());
            return;
        }

        $line->addError('Internal Error. Please notify developer. ERR_CODE: IM001');
        $line->updateStatus(ImportLineStatus::ERROR());
    }
}
