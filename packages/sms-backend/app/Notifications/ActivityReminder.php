<?php

namespace App\Notifications;

use App\Channels\ExpoChannel;
use App\Classes\ExpoMessage;
use App\Classes\NotificationData;
use App\Enums\NotificationType;
use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ActivityReminder extends Notification implements ShouldQueue
{

    use Queueable;

    public function __construct(public Activity $activity)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database'];

        if ($notifiable->notificationDevices()->count() > 0) {
            $via[] = ExpoChannel::class;
        }

        return $via;
    }

    /**
     * Get the database representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return NotificationData::activityReminder($this->activity)->toArray();
    }

    public function toExpo($notifiable)
    {
        $type = NotificationType::ActivityReminder();
        return ExpoMessage::create()
            ->addRecipients($notifiable)
            ->setBadgeFor($notifiable)
            ->title("Activity Reminder")
            ->body($this->activity->reminder_note)
            ->code($type->key)
            ->link(sprintf(config("notification-link.{$type->key}"), $this->activity->id));
    }
}
