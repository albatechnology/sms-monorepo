<?php

namespace Database\Factories;

use App\Models\ChannelCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ChannelCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ChannelCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'name'          => $this->faker->company,
            'code'          => Str::slug($this->faker->numerify('CODE ###')),
            'slug'          => Str::slug($this->faker->company),
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}
