<?php

namespace Database\Factories;

use App\Models\SupervisorType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SupervisorTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SupervisorType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'code' => Str::slug($this->faker->name),
        ];
    }
}
