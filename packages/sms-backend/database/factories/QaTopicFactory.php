<?php

namespace Database\Factories;

use App\Models\QaMessage;
use App\Models\QaTopic;
use App\Models\QaTopicUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QaTopicFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QaTopic::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'subject'    => $this->faker->sentence,
            'creator_id' => User::first()->id ?? User::factory()->create()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function withMessages()
    {
        return $this->afterCreating(function (QaTopic $topic) {
            $topic->subscribers->each(function (QaTopicUser $subscriber) use ($topic) {
                QaMessage::factory()->forTopic($topic)->create(['sender_id' => $subscriber->user_id]);
            });
        });
    }

    public function withUsers(int ...$user_ids)
    {
        return $this->afterCreating(function (QaTopic $topic) use ($user_ids) {
            $topic->users()->syncWithoutDetaching($user_ids);
        });
    }
}
