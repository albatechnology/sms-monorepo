<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\ProductVersion;

class ProductVersionFactory extends BaseFactory
{
    protected $model = ProductVersion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'height' => $this->faker->numberBetween(100, 1000),
            'width' => $this->faker->numberBetween(100, 1000),
            'length' => $this->faker->numberBetween(100, 1000),
            'company_id' => Company::first()->id ?? Company::factory()->create()->id,
        ];
    }
}
