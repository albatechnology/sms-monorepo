<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\ProductTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductTagFactory extends BaseFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductTag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'       => $this->faker->department,
            'company_id' => Company::first()->id ?? Company::factory()->create()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
