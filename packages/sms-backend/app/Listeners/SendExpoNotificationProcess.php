<?php

namespace App\Listeners;

use App\Classes\ExpoMessage;
use App\Events\SendExpoNotification;
use App\Services\PushNotificationService;

class SendExpoNotificationProcess
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
     * @param  SendExpoNotification  $event
     * @return void
     */
    public function handle(SendExpoNotification $event)
    {
        $message = ExpoMessage::create()
            ->addRecipients($event->data['receipents'])
            ->setBadgeFor($event->data['badge_for'])
            ->title($event->data['title'])
            ->body($event->data['body'])
            ->code($event->data['code'])
            ->link($event->data['link']);
        app(PushNotificationService::class)->notify($message);
    }
}
