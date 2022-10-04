<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Covering;
use App\Models\Product;

class CoveringFactory extends BaseFactory
{
    protected $model = Covering::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'       => $this->faker->word,
            'type'       => $this->faker->randomElement(['leather', 'soft cover']),
            'product_id' => Product::first()->id ?? Product::factory()->create()->id,
            'company_id' => Company::first()->id ?? Company::factory()->create()->id,
        ];
    }
}
