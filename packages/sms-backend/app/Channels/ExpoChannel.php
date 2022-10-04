<?php


namespace App\Channels;

use App\Classes\ExpoMessage;
use App\Services\PushNotificationService;
use Illuminate\Notifications\Notification;

class ExpoChannel
{

    public function __construct(protected PushNotificationService $service)
    {
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        /** @var ExpoMessage $message */
        $message = $notification->toExpo($notifiable);
        $this->service->notify($message);
    }
}