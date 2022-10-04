<?php

namespace Database\Factories;

use App\Models\QaMessage;
use App\Models\QaTopic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QaMessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QaMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $topic = QaTopic::first() ?? QaTopic::factory()->create();

        return [
            'content'    => $this->faker->sentence,
            'topic_id'   => $topic->id,
            'sender_id'  => User::first()->id ?? User::factory()->create()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function forTopic($topic)
    {
        return $this->state(
            [
                'topic_id' => $topic->id,
            ]
        );
    }
//
//    public function forTopic($topic)
//    {
//        return $this->state(
//            [
//                'topic_id'  => $topic,
//            ]
//        );
//    }
}
