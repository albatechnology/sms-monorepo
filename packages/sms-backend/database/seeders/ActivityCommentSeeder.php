<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityComment;
use Illuminate\Database\Seeder;

class ActivityCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ActivityComment::flushEventListeners();

        Activity::all()->each(function (Activity $activity) {
            $models = ActivityComment::factory()->count(3)->for($activity)->create();

            ActivityComment::factory()->replyFor($models->first())->create();

            $activity->refreshCommentStats();
        });
    }
}
