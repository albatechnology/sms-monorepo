<?php

namespace App\Pipes\Order\Admin;

use App\Enums\OrderApprovalStatus;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\OrderStockStatus;
use App\Models\Address;
use App\Models\Lead;
use App\Models\Order;
use App\Models\User;
use Closure;
use Exception;

/**
 * Fill order attributes from raw source
 *
 * Class FillOrderAttributes
 * @package App\Pipes\Order\Admin
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

        $user = User::findOrFail(request()->user_id);
        $lead = Lead::findOrFail($order->raw_source['lead_id']);

        $order->note                           = $order->raw_source['note'] ?? null;
        $order->lead_id                        = $order->raw_source['lead_id'];
        $order->shipping_fee                   = $order->raw_source['shipping_fee'] ?? 0;
        $order->packing_fee                    = $order->raw_source['packing_fee'] ?? 0;
        $order->additional_discount            = $order->raw_source['additional_discount'] ?? 0;
        $order->expected_shipping_datetime     = $order->raw_source['expected_shipping_datetime'] ?? null;
        $order->quotation_valid_until_datetime = $order->raw_source['quotation_valid_until_datetime'] ?? now()->addMinutes(config('quotation_valid_for_minutes'));
        $order->user_id                        = $user->id;
        $order->customer_id                    = $lead->customer_id;
        $order->channel_id                     = $user->channel_id;
        $order->company_id                     = $user->company_id;
        $order->interior_design_id             = $order->raw_source['interior_design_id'] ?? null;
        $order->sum_total_discount             = 0;
        $order->total_discount                 = 0;
        $order->status                         = OrderStatus::QUOTATION();
        $order->stock_status                   = OrderStockStatus::INDENT();
        $order->approval_status                = OrderApprovalStatus::NOT_REQUIRED();
        $order->payment_status                 = OrderPaymentStatus::NONE();
        $order->expected_price                 = $order->raw_source['expected_price'] ?? null;

        $address = $lead->customer->defaultCustomerAddress ? Address::findOrFail($lead->customer->defaultCustomerAddress->id)->toRecord() : Address::findOrFail($lead->customer->customerAddresses->first()->id)->toRecord();

        $record['billing_address']  = $address;
        $record['shipping_address'] = $address;
        $record['tax_invoice']      = null;
        $order->records             = $record;

        return $next($order);
    }
}
