<?php

namespace Database\Factories;

use App\Models\LeadCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LeadCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'        => $this->faker->word,
            'description' => $this->faker->sentence,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }
}
