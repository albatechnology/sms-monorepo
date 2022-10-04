<?php

namespace App\Pipes\Reportable;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\TargetType;
use App\Models\Payment;

/**
 *
 * Class DealsPaymentPrice
 * @package App\Pipes\Discountable
 */
class DealsPaymentPrice extends BaseReportablePipe
{
    final protected function getTargetType(): TargetType
    {
        return TargetType::DEALS_PAYMENT_PRICE();
    }

    final protected function getReportableClassName(): string
    {
        return Payment::class;
    }

    protected function getReportableValueProperty(): string
    {
        return 'amount';
    }

    protected function whereReportableBaseQuery($query)
    {
        return $query

            // payment for order that is deal
            ->whereHas('order', function ($q) {
                $q->where('deal_at', '>', $this->report->start_date)
                    ->where('deal_at', '<', $this->report->end_date)
                    ->whereNotIn('status', [OrderStatus::CANCELLED]);
            })

            // only count payment that has been approved
            ->where('status', PaymentStatus::APPROVED);
    }

    protected function whereReportableChannel($query, $id)
    {
        return $query->whereHas('order', function ($q) use ($id) {
            $q->where('channel_id', $id);
        });
    }

    protected function whereReportableUsers($query, array $ids)
    {
        return $query->whereHas('order', function ($q) use ($ids) {
            // we group the payment based on the user related to the order
            $q->whereIn('user_id', $ids);
        });
    }
}