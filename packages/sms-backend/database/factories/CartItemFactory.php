<?php

namespace Database\Factories;

use App\Classes\CartItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CartItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        foreach (range(0, $this->faker->numberBetween(0, 3)) as $i) {
            $data[$i] = \App\Classes\CartItemLine::factory()->make();
        }

        return $data;
    }
}
