<?php

namespace App\Imports;

use App\Jobs\UpdateProductModelPriceRange;
use App\Models\ImportLine;
use App\Models\ProductModel;
use App\Models\ProductUnit;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductUnitImport extends BaseImport implements ToModel, WithHeadingRow
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
                'Test product unit',
                'Unit description',
                10000000, 0,
                1,
                1, 1, 1, //relationships
                '123.32.132.32.13',
            ]
        ]);
    }

    public static function getHeader(): array
    {
        return [
            'name', 'description', 'price', 'production_cost', 'is_active',
            'product_id', 'colour_id', 'covering_id',
            'sku'
        ];
    }

    public static function getModelName(): string
    {
        return ProductUnit::class;
    }

    /**
     * This gets executed after the completion of the whole batch import
     */
    public function afterBatchImport()
    {
        UpdateProductModelPriceRange::dispatch();
    }

    /**
     * Create model based on import line
     * @param Collection $lines
     * @return mixed
     */
    protected function insertModel(Collection $lines): void
    {
        $array = $this->insertArray($lines);
        ProductUnit::insert($array);

        $product_ids = collect($array)->pluck('product_id');

        ProductModel::query()
            ->whereHas('products', function ($query) use ($product_ids) {
                $query->whereIn('id', $product_ids);
            })
            ->update(['need_price_range_update' => 1]);
    }

    /**
     * Create model based on import line
     * @param ImportLine $line
     * @return mixed
     */
    protected function createModel(ImportLine $line): void
    {
        $unit = ProductUnit::create($this->createArray($line));
        $unit->updateProductBrandPriceRange();
    }

    protected function createArray(ImportLine $line): array
    {
        return [
            'name'            => $line->data['name'],
            'company_id'      => $this->batch->company_id,
            'description'     => $line->data['description'] ?? null,
            'price'           => $line->data['price'],
            'production_cost' => $line->data['production_cost'],
            'is_active'       => $line->data['is_active'] ?? 0,
            'product_id'      => $line->data['product_id'],
            'colour_id'       => $line->data['colour_id'],
            'covering_id'     => $line->data['covering_id'],
            'sku'             => $line->data['sku'],
        ];
    }

    /**
     * @param ImportLine $line
     * @return mixed
     */
    protected function updateModel(ImportLine $line): void
    {
        /** @var ProductUnit $unit */
        $unit = ProductUnit::query()
            ->where(static::getUniqueKey(), $line->data[static::getUniqueKey()])
            ->where('company_id', $this->batch->company_id)
            ->firstOrFail();

        $unit->update(static::updateArray($line));
        $unit->updateProductBrandPriceRange();
    }

    public static function getUniqueKey(): string
    {
        return 'sku';
    }

    protected function updateArray(ImportLine $line): array
    {
        return [
            'name'            => $line->data['name'],
            'description'     => $line->data['description'] ?? null,
            'price'           => $line->data['price'],
            'production_cost' => $line->data['production_cost'],
            'is_active'       => $line->data['is_active'] ?? 0,
            'product_id'      => $line->data['product_id'],
            'colour_id'       => $line->data['colour_id'],
            'covering_id'     => $line->data['covering_id'],
        ];
    }

    protected function getValidationRule(array $data): array
    {
        return [
            'name'            => 'required|min:1',
            'description'     => 'nullable|min:1',
            'price'           => 'required|integer|min:1',
            'production_cost' => 'nullable|integer|min:0',
            'is_active'       => 'nullable|boolean',
            'product_id'      => [
                'required',
                Rule::exists('products', 'id')
                    ->where('company_id', $this->batch->company_id)
            ],
            'colour_id'       => [
                'required',
                Rule::exists('colours', 'id')
                    ->where('product_id', $data['product_id'] ?? 0)
                    ->where('company_id', $this->batch->company_id)
            ],
            'covering_id'     => [
                'required',
                Rule::exists('coverings', 'id')
                    ->where('product_id', $data['product_id'] ?? 0)
                    ->where('company_id', $this->batch->company_id)
            ],
            'sku'             => 'required|min:1',
        ];
    }
}
