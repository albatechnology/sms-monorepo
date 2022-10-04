<?php

namespace Database\Factories;

use App\Enums\UserType;
use App\Models\Channel;
use App\Models\Company;
use App\Models\SupervisorType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'              => $this->faker->name,
            'email'             => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token'    => Str::random(10),
            'type'              => UserType::DEFAULT,
            'company_id'        => Company::first()->id ?? Company::factory()->create()->id,
        ];
    }

    public function supervisedBy(User $user)
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'type'          => UserType::SALES,
                'supervisor_id' => $user->id,
            ];
        });
    }

    public function supervisor()
    {
        return $this->state(function (array $attributes) {
            return [
                'type'               => UserType::SUPERVISOR,
                'supervisor_type_id' => SupervisorType::first()->id ?? SupervisorType::factory()->create()->id
            ];
        });
    }

    public function sales()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => UserType::SALES,
            ];
        });
    }

    public function withChannel()
    {
        return $this->afterCreating(function (User $user) {
            $channel = Channel::first() ?? Channel::factory()->create();
            $user->channels()->syncWithoutDetaching([$channel->id]);
            $user->update(["channel_id" => $channel->id]);
        });
    }
}
