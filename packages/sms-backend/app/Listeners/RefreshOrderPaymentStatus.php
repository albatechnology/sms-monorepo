<?php

namespace App\Listeners;

use App\Models\Payment;

class RefreshOrderPaymentStatus
{
    public function __construct()
    {
        //
    }

    public function handle($event)
    {
        /** @var Payment $payment */
        $payment = $event->model;

        $payment->order->refreshPaymentStatus();
    }
}
