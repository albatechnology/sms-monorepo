<?php

namespace App\Imports;

use App\Models\ImportLine;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport extends BaseImport implements ToModel, WithHeadingRow
{
    public static function getModelName(): string
    {
        return Product::class;
    }

    /**
     * May provide export sample data
     * @return Collection
     */
    public static function getExportCollection(): Collection
    {
        return Collect([
            self::getHeader(),
            [
                'Test product', 1,
                1, 1, 1, 1
            ]
        ]);
    }

    public static function getHeader(): array
    {
        return [
            'name', 'is_active',
            'product_brand_id',
            'product_model_id',
            'product_version_id',
            'product_category_code_id',
        ];
    }

    protected function updateArray(ImportLine $line): array
    {
        return [
            'name'                     => $line->data['name'],
            'is_active'                => $line->data['is_active'] ?? 0,
            'product_brand_id'         => $line->data['product_brand_id'],
            'product_model_id'         => $line->data['product_model_id'],
            'product_version_id'       => $line->data['product_version_id'],
            'product_category_code_id' => $line->data['product_category_code_id'],
        ];
    }

    protected function createArray(ImportLine $line): array
    {
        return [
            'name'                     => $line->data['name'],
            'company_id'               => $this->batch->company_id,
            'is_active'                => $line->data['is_active'] ?? 0,
            'product_brand_id'         => $line->data['product_brand_id'],
            'product_model_id'         => $line->data['product_model_id'],
            'product_version_id'       => $line->data['product_version_id'],
            'product_category_code_id' => $line->data['product_category_code_id'],
        ];
    }

    protected function getValidationRule(array $data): array
    {
        return [
            'name'                     => 'required|min:1',
            'product_brand_id'         => [
                'required',
                Rule::exists('product_brands', 'id')
                    ->where('company_id', $this->batch->company_id)
            ],
            'product_model_id'         => [
                'required',
                Rule::exists('product_models', 'id')
                    ->where('company_id', $this->batch->company_id)
            ],
            'product_version_id'       => [
                'required',
                Rule::exists('product_versions', 'id')
                    ->where('company_id', $this->batch->company_id)
            ],
            'product_category_code_id' => [
                'required',
                Rule::exists('product_category_codes', 'id')
                    ->where('company_id', $this->batch->company_id)
            ]
        ];
    }
}
