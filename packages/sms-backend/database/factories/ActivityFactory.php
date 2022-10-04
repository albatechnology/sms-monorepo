<?php

namespace Database\Factories;

use App\Enums\ActivityFollowUpMethod;
use App\Enums\ActivityStatus;
use App\Models\Activity;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $lead = Lead::first() ?? Lead::factory()->create();

        return [
            'follow_up_datetime' => now(),
            'follow_up_method'   => $this->faker->randomElement(ActivityFollowUpMethod::getSelectableInstances())->value,
            'feedback'           => $this->faker->text($maxNbChars = 150),
            'status'             => ActivityStatus::getRandomValue(),
            'lead_id'            => $lead->id,
            'customer_id'        => $lead->customer_id,
            'user_id'            => User::first()->id ?? User::factory()->create()->id,
            'created_at'         => now(),
            'updated_at'         => now(),
        ];
    }
}
