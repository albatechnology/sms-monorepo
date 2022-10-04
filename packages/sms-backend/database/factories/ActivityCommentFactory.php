<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\User;

class ActivityCommentFactory extends BaseFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActivityComment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'content'     => $this->faker->text($maxNbChars = 100),
            'activity_id' => Activity::first()->id ?? Activity::factory()->create()->id,
            'user_id'     => User::first()->id ?? User::factory()->create()->id,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }

    public function forActivity(Activity $activity)
    {
        return $this->state(
            [
                'activity_id' => $activity->id,
            ]
        );
    }

    public function replyFor(ActivityComment $comment)
    {
        return $this->state(
            [
                "activity_id"         => $comment->activity_id,
                'activity_comment_id' => $comment->id,
            ]
        );
    }
}
