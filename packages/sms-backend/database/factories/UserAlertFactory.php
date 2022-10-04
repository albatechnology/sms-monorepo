<?php

namespace Database\Factories;

use App\Models\UserAlert;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAlertFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserAlert::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'alert_text'    => $this->faker->text(10),
            'alert_link'    => $this->faker->url,
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}
