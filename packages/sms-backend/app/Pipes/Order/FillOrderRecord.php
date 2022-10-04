<?php

namespace App\Pipes\Order;

use App\Models\Address;
use App\Models\Order;
use Closure;

/**
 * Fill relationship data into record property
 *
 * Class FillOrderAttributes
 * @package App\Pipes\Order
 */
class FillOrderRecord
{
    public function handle(Order $order, Closure $next)
    {
        // we are not storing record for lead, user, customer and channel
        $record['billing_address']  = $order->raw_source['billing_address_id'] ? Address::findOrFail($order->raw_source['billing_address_id'])->toRecord() : null;
        $record['shipping_address'] = $order->raw_source['shipping_address_id'] ? Address::findOrFail($order->raw_source['shipping_address_id'])->toRecord() : null;
        // $record['tax_invoice']      = !empty($order->raw_source['tax_invoice_id']) ? TaxInvoice::findOrFail($order->raw_source['tax_invoice_id'])->toArray() : null;
        $record['tax_invoice']      = null;
        $order->records             = $record;

        return $next($order);
    }
}
