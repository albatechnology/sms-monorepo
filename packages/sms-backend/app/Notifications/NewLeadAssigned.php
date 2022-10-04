<?php

namespace App\Notifications;

use App\Channels\ExpoChannel;
use App\Classes\ExpoMessage;
use App\Classes\NotificationData;
use App\Enums\NotificationType;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewLeadAssigned extends Notification implements ShouldQueue
{

    use Queueable;

    public function __construct(public Lead $activity)
    {
        //
    }
}
