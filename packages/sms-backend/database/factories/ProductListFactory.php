<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductList;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductListFactory extends Factory
{
    protected $model = ProductList::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = Product::first() ?? Product::factory()->create();
        return [
            'model_id'   => $product->product_brand_id,
            'model_type' => ProductBrand::class,
            'company_id' => $product->company_id,
        ];
    }
}
