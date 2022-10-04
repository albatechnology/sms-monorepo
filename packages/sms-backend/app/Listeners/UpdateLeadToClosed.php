<?php

namespace App\Listeners;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Events\OrderPaymentSettlement;

class UpdateLeadToClosed
{
    public function __construct()
    {
    }

    public function handle(OrderPaymentSettlement $event)
    {
        $event->order->lead->update(
            [
                // 'type'   => LeadType::CLOSED(),
                'type'   => LeadType::DEAL(),
                'status' => LeadStatus::SALES()
            ]
        );
    }
}
