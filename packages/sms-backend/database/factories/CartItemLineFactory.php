<?php

namespace Database\Factories;

use App\Classes\CartItemLine;
use App\Models\Colour;
use App\Models\Covering;
use App\Models\ProductUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemLineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CartItemLine::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product_unit = ProductUnit::first() ?? ProductUnit::factory()->create();

        return [
            'id'         => $product_unit->id,
            'quantity'   => $this->faker->numberBetween(1, 5),
            'sku'         => $product_unit->sku,
            'product_id' => $product_unit->product_id,
            'name'       => $product_unit->name,
            'price'      => $product_unit->price,
            'colour'     => Colour::first()->toArray() ?? Colour::factory()->create()->toArray(),
            'covering'   => Covering::first()->toArray() ?? Covering::factory()->create()->toArray(),
        ];
    }
}
