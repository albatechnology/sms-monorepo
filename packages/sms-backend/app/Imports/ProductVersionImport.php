<?php

namespace App\Imports;

use App\Models\ImportLine;
use App\Models\ProductVersion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductVersionImport extends BaseImport implements ToModel, WithHeadingRow
{

    public static function getModelName(): string
    {
        return ProductVersion::class;
    }

    public static function getHeader(): array
    {
        return [
            'name', 'height', 'width', 'length'
        ];
    }

    protected function getValidationRule(array $data): array
    {
        return [
            'name'   => 'required|min:1',
            'height' => 'nullable',
            'width'  => 'nullable',
            'length' => 'nullable',
        ];
    }

    protected function updateArray(ImportLine $line): array
    {
        return [
            'name'   => $line->data['name'],
            'height' => $line->data['height'] ?? null,
            'width'  => $line->data['width'] ?? null,
            'length' => $line->data['length'] ?? null,
        ];
    }

    protected function createArray(ImportLine $line): array
    {
        return [
            'name'       => $line->data['name'],
            'height'     => $line->data['height'],
            'width'      => $line->data['width'],
            'length'     => $line->data['length'],
            'company_id' => $this->batch->company_id,
        ];
    }
}
