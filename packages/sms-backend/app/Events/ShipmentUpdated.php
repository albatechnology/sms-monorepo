<?php

namespace App\Events;

use App\Models\Shipment;
use Illuminate\Foundation\Events\Dispatchable;

class ShipmentUpdated
{
    use Dispatchable;

    public function __construct(public Shipment $shipment)
    {
        //
    }
}