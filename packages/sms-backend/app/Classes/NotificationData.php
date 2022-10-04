<?php


namespace App\Classes;


use App\Models\Activity;
use Illuminate\Support\Str;

class NotificationData
{
    public function __construct(public $title, public $body, public $link,)
    {

    }

    public static function activityReminder(Activity $activity): self
    {
        return new self(
            "Activity Reminder",
            $activity->reminder_note ?? "no note",
            Str::of(config('notification-link.ActivityReminder'))->replace('{id}', $activity->id)
        );
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'link' => $this->link,
        ];
    }
}