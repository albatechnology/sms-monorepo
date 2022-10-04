<?php

namespace App\Notifications;

use App\Models\Discount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DiscountApproval extends Notification implements ShouldQueue
{

    use Queueable;

    public function __construct(public Discount $activity)
    {
        //
    }
}
