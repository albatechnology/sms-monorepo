<?php

namespace Database\Factories;

use App\Enums\ProductCategoryType;
use App\Models\Company;
use App\Models\ProductCategory;

class ProductCategoryFactory extends BaseFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'        => $this->faker->department,
            'description' => $this->faker->sentence,
            'type'        => ProductCategoryType::getRandomValue(),
            'company_id'  => Company::first()->id ?? Company::factory()->create()->id
        ];
    }

}
