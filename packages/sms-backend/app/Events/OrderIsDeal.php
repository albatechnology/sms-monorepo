<?php

namespace App\Events;

use App\Models\Order;
use App\Traits\ReportableEvent;
use Illuminate\Foundation\Events\Dispatchable;

class OrderIsDeal
{
    use Dispatchable, ReportableEvent;

    public function __construct(public Order $model)
    {

    }
}