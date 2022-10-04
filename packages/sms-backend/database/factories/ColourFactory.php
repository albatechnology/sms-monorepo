<?php

namespace Database\Factories;

use App\Models\Colour;
use App\Models\Company;
use App\Models\Product;

class ColourFactory extends BaseFactory
{
    protected $model = Colour::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'        => $this->faker->colorName,
            'description' => $this->faker->sentence,
            'product_id'  => Product::first()->id ?? Product::factory()->create()->id,
            'company_id'  => Company::first()->id ?? Company::factory()->create()->id,
        ];
    }
}
