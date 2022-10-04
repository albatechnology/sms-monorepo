<?php

namespace App\Pipes\Reportable;

use App\Classes\TargetMapContext;
use App\Enums\OrderStatus;
use App\Enums\TargetType;
use App\Interfaces\Reportable;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductBrand;

/**
 *
 * Class DealsBrandPrice
 * @package App\Pipes\Reportable
 */
class DealsBrandPrice extends BaseReportablePipe
{

    final protected function getTargetType(): TargetType
    {
        return TargetType::DEALS_BRAND_PRICE();
    }

    final protected function getReportableClassName(): string
    {
        return Order::class;
    }

    protected function getTargetContext(?Reportable $model = null): ?array
    {
        /** @var Order $order */
        $order = $model ?? $this->model;

        $orderDetails = $order->order_details;
        $data         = [];

        // loop through all order
        return $orderDetails
            ->reduce(function ($carry, OrderDetail $detail) {
                $brand = $detail->getBrandRecord();
                $label = $brand['name'];
                $id    = $brand['id'];
                $value = $detail->getReportPrice();

                if ($carry[$id] ?? false) {
                    // need to sum with existing data
                    $newContext = TargetMapContext::make($id, ProductBrand::class, $label, $value);
                    $context    = TargetMapContext::fromArray($carry[$id])
                        ->combine($newContext);
                    //$context = TargetMapContext::make($id, ProductBrand::class, $label, $value );

                } else {
                    $context = TargetMapContext::make($id, ProductBrand::class, $label, $value);
                }

                $carry[$id] = $context->toArray();

                return $carry;
            }, []);
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

    protected function getReportableValueProperty(): string
    {
        return 'total_price';
    }
}
