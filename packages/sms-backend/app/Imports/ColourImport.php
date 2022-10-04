<?php

namespace App\Imports;

use App\Models\Colour;
use App\Models\ImportLine;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ColourImport extends BaseImport implements ToModel, WithHeadingRow
{

    /**
     * May provide export sample data
     * @return Collection
     */
    public static function getExportCollection(): Collection
    {
        return Collect([
            self::getHeader(),
            [
                'turquoise', '66B2', 1
            ]
        ]);
    }

    public static function getHeader(): array
    {
        return [
            'name', 'description', 'product_id'
        ];
    }

    public static function getModelName(): string
    {
        return Colour::class;
    }

    protected function updateArray(ImportLine $line): array
    {
        return [
            'name'        => $line->data['name'],
            'description' => $line->data['description'] ?? '',
        ];
    }

    protected function createArray(ImportLine $line): array
    {
        return [
            'name'        => $line->data['name'],
            'description' => $line->data['description'] ?? '',
            'product_id'  => $line->data['product_id'],
            'company_id'  => $this->batch->company_id,
        ];
    }

    protected function getValidationRule(array $data): array
    {
        return [
            'name'        => 'required|min:1',
            'description' => 'required|min:1',
            'product_id'  => [
                'required',
                Rule::exists('products', 'id')
                    ->where('company_id', $this->batch->company_id)
            ],
        ];
    }
}
