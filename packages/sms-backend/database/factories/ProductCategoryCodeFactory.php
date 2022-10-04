<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\ProductCategoryCode;

class ProductCategoryCodeFactory extends BaseFactory
{
    protected $model = ProductCategoryCode::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'CAT ' . ($this->faker->numberBetween(1, 20) * 5),
            'company_id' => Company::first()->id ?? Company::factory()->create()->id,
        ];
    }
}
