<?php

namespace App\Listeners;

use App\Enums\LeadType;
use App\Events\OrderCreated;

class UpdateLeadToProspect
{
    public function __construct()
    {
        //
    }

    public function handle(OrderCreated $event)
    {
        $lead = $event->order->lead;

        if ($lead->type->is(LeadType::LEADS)) {
            $lead->type = LeadType::PROSPECT();
            $lead->save();
        }
    }
}
