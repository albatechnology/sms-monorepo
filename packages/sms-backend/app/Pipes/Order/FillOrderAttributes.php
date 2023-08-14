<?php

namespace App\Pipes\Order;

use App\Enums\OrderApprovalStatus;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\OrderStockStatus;
use App\Models\Lead;
use App\Models\Order;
use Closure;
use Exception;

/**
 * Fill order attributes from raw source
 *
 * Class FillOrderAttributes
 * @package App\Pipes\Order
 */
class FillOrderAttributes
{
    /**
     * @param Order $order
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Order $order, Closure $next): mixed
    {
        if (empty($order->raw_source)) throw new Exception('Order raw source is not initiated!');

        $user = $order->user ?? user();

        $order->note                           = $order->raw_source['note'] ?? null;
        $order->lead_id                        = $order->raw_source['lead_id'];
        $order->shipping_fee                   = (int) isset($order->raw_source['shipping_fee']) ? $order->raw_source['shipping_fee'] : 0;
        $order->packing_fee                    = (int) isset($order->raw_source['packing_fee']) ? $order->raw_source['packing_fee'] : 0;
        $order->additional_discount            = (int) isset($order->raw_source['additional_discount']) ? $order->raw_source['additional_discount'] : 0;
        $order->expected_shipping_datetime     = isset($order->raw_source['expected_shipping_datetime']) ? $order->raw_source['expected_shipping_datetime'] : null;
        $order->quotation_valid_until_datetime = isset($order->raw_source['quotation_valid_until_datetime']) ? $order->raw_source['quotation_valid_until_datetime'] : now()->addMinutes(config('quotation_valid_for_minutes'));
        $order->subscribtion_user_id           = $user->subscribtion_user_id;
        $order->user_id                        = $user->id;
        $order->customer_id                    = Lead::findOrFail($order->raw_source['lead_id'])->customer_id;
        $order->channel_id                     = $user->channel_id;
        // $order->company_id                     = $user->company_id;
        $order->sum_total_discount             = 0;
        $order->total_discount                 = 0;
        // $order->sum_total_voucher             = 0;
        // $order->total_voucher                 = 0;
        $order->status                         = OrderStatus::QUOTATION();
        $order->stock_status                   = OrderStockStatus::INDENT();
        $order->approval_status                = OrderApprovalStatus::NOT_REQUIRED();
        $order->payment_status                 = OrderPaymentStatus::NONE();
        $order->expected_price                 = $order->raw_source['expected_price'] ?? null;
        $order->is_direct_purchase             = $order->raw_source['is_direct_purchase'] ?? 0;

        return $next($order);
    }
}
