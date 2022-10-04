<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\ProductModel;

class ProductModelFactory extends BaseFactory
{
    protected $model = ProductModel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->word;
        return [
            'name'        => $name,
            'description' => $this->faker->sentence,
            'company_id'  => Company::first()->id ?? Company::factory()->create()->id,
        ];
    }
}
