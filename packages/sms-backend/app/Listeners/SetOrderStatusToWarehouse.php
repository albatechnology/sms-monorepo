<?php

namespace App\Listeners;

use App\Events\OrderPaymentSettlement;
use App\Services\OrderService;

class SetOrderStatusToWarehouse
{
    public function __construct()
    {
        //
    }

    public function handle(OrderPaymentSettlement $event)
    {
        app(OrderService::class)->setOrderStatusToShipment($event->order);
    }
}
