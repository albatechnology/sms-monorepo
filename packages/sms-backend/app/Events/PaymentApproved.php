<?php

namespace App\Events;

use App\Models\Payment;
use App\Traits\ReportableEvent;
use Illuminate\Foundation\Events\Dispatchable;

class PaymentApproved
{
    use Dispatchable, ReportableEvent;

    public function __construct(public Payment $model)
    {
        //
    }
}