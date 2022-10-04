<?php

namespace App\Listeners;

use App\Events\ShipmentUpdated;
use App\Services\OrderService;

class UpdateOrderShippingStatus
{
    public function __construct()
    {
        //
    }

    public function handle(ShipmentUpdated $event)
    {
        OrderService::evaluateShippingStatus($event->shipment->order);
    }
}
