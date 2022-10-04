<?php

namespace Database\Factories;

use App\Models\Colour;
use App\Models\Company;
use App\Models\Covering;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Stock;

class ProductUnitFactory extends BaseFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductUnit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = Product::first() ?? Product::factory()->create();
        //$colour   = Colour::where('product_id', $product->id)->first();
        //$covering = Covering::where('product_id', $product->id)->first();

        return [
            'name'        => $this->faker->productName,
            'price'       => $this->faker->randomNumber(3) * 100000,
            'is_active'   => 1,
            'product_id'  => $product->id,
            'company_id'  => Company::first()->id ?? Company::factory()->create()->id,
            'colour_id'   => Colour::factory()->create(['product_id' => $product->id])->id,
            'covering_id' => Covering::factory()->create(['product_id' => $product->id])->id,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }

    public function forProduct(Product $product)
    {
        return $this->state(
            [
                'name'        => $product->name,
                'product_id'  => $product->id,
                'company_id'  => $product->company_id,
                'colour_id'   => Colour::factory()->create(['product_id' => $product->id])->id,
                'covering_id' => Covering::factory()->create(['product_id' => $product->id])->id,
            ]
        );
    }

    public function withStock(int $stock = 1000)
    {
        return $this->afterCreating(function (ProductUnit $unit) use ($stock) {
            $unit->stocks->each(function (Stock $stockModel) use ($stock) {
                $stockModel->addStock($stock);
            });
        });
    }
}
