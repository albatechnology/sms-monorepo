<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategoryCode;
use App\Models\ProductModel;
use App\Models\ProductUnit;
use App\Models\ProductVersion;
use Illuminate\Support\Collection;

class ProductFactory extends BaseFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->productName;
        return [
            'name'                     => $this->faker->productName,
            'is_active'                => $this->faker->numberBetween(0, 1),
            'price'                    => $this->faker->randomNumber(3) * 100000,
            'company_id'               => Company::first()->id ?? Company::factory()->create()->id,
            'product_brand_id'         => ProductBrand::first()->id ?? ProductBrand::factory()->create()->id,
            'product_model_id'         => ProductModel::first()->id ?? ProductModel::factory()->create()->id,
            'product_version_id'       => ProductVersion::first()->id ?? ProductVersion::factory()->create()->id,
            'product_category_code_id' => ProductCategoryCode::factory()->create()->id,
            'created_at'               => now(),
            'updated_at'               => now(),
        ];
    }

    public function withProductUnits()
    {
        return $this->afterCreating(function (Product $product) {
            ProductUnit::factory()
                ->forProduct($product)
                ->count(2)
                ->create();
        });
    }

    public function withTags(Collection $tags)
    {
        return $this->afterCreating(function (Product $product) use ($tags) {
            $product->tags()->sync($tags->pluck('id'));
        });
    }
}
