<?php

namespace App\Imports;

use App\Models\ImportLine;
use App\Models\ProductModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductModelImport extends BaseImport implements ToModel, WithHeadingRow
{
    public static function getHeader(): array
    {
        return [
            'name', 'description'
        ];
    }

    public static function getModelName(): string
    {
        return ProductModel::class;
    }

    protected function updateArray(ImportLine $line): array
    {
        return [
            'name'        => $line->data['name'],
            'description' => $line->data['description'] ?? null
        ];
    }

    protected function createArray(ImportLine $line): array
    {
        return [
            'name'        => $line->data['name'],
            'description' => $line->data['description'] ?? null,
            'company_id'  => $this->batch->company_id,
        ];
    }

    protected function getValidationRule(array $data): array
    {
        return [
            'name'        => 'required|min:1',
            'description' => 'nullable'
        ];
    }
}
