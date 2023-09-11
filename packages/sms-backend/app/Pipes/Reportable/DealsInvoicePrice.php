<?php

namespace App\Pipes\Reportable;

use App\Enums\OrderStatus;
use App\Enums\TargetType;
use App\Models\Order;

/**
 *
 * Class DealsInvoicePrice
 * @package App\Pipes\Discountable
 */
class DealsInvoicePrice extends BaseReportablePipe
{
    final protected function getTargetType(): TargetType
    {
        return TargetType::DEALS_INVOICE_PRICE();
    }

    final protected function getReportableClassName(): string
    {
        return Order::class;
    }

    protected function getReportableValueProperty(): string
    {
        return 'total_price';
    }

    protected function whereReportableBaseQuery($query)
    {
        // order that is deal
        return $query->where('deal_at', '>', $this->report->start_date)
            ->where('deal_at', '<', $this->report->end_date)
            ->whereNotIn('status', [OrderStatus::CANCELLED]);
    }

    protected function whereReportableChannel($query, $id)
    {
        return $query->where('channel_id', $id);
    }

    protected function whereReportableUsers($query, array $ids)
    {
        return $query->whereIn('user_id', $ids);
    }
}
