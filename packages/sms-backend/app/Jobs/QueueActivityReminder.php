<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Notifications\ActivityReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueActivityReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Activity $activity)
    {
    }

    public function handle()
    {
        // reminder removed
        if (empty($this->activity->reminder_datetime)) {
            return;
        }

        // reminder has been changed
        if ($this->activity->reminder_datetime->isAfter(now())) {
            return;
        }

        $this->activity->user->notify(new ActivityReminder($this->activity));

        $this->activity->reminder_datetime = null;
        $this->activity->save();
    }
}