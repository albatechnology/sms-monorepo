<?php

namespace App\Imports;

use App\Models\ImportLine;
use App\Models\ProductCategoryCode;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductCategoryCodeImport extends BaseImport implements ToModel, WithHeadingRow
{
    public static function getModelName(): string
    {
        return ProductCategoryCode::class;
    }

    public static function getHeader(): array
    {
        return [
            'name'
        ];
    }

    protected function updateArray(ImportLine $line): array
    {
        return [
            'name' => $line->data['name'],
        ];
    }

    protected function createArray(ImportLine $line): array
    {
        return [
            'name'       => $line->data['name'],
            'company_id' => $this->batch->company_id,
        ];
    }

    protected function getValidationRule(array $data): array
    {
        return [
            'name' => 'required|min:1'
        ];
    }
}
