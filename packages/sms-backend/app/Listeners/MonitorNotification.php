<?php

namespace App\Listeners;

use App\Notifications\ActivityReminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\InteractsWithQueue;

class MonitorNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NotificationSent  $event
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        // $event->channel
        // $event->notifiable
        // $event->notification
        // $event->response

        if($event->notification instanceof ActivityReminder){
            // change the reminder flag to sent
            $event->notification->activity->update(['reminder_sent' => null]);
        }
    }
}
