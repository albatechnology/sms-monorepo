<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\ProductBrand;

class ProductBrandFactory extends BaseFactory
{
    protected $model = ProductBrand::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->word;
        return [
            'name'       => $name,
            'company_id' => Company::first()->id ?? Company::factory()->create()->id,
        ];
    }

    public function sample()
    {
        return $this->state([
            'name' => 'Brand Name 123'
        ]);
    }
}
