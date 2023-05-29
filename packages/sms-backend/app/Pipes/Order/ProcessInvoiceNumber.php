<?php

namespace App\Pipes\Order;

// use App\Models\CompanyData;
use App\Models\Order;
use Closure;

/**
 * Generate and save invoice number for this order.
 *
 * Class ProcessInvoiceNumber
 * @package App\Pipes\Order
 */
class ProcessInvoiceNumber
{
    public function handle(Order $order, Closure $next)
    {
        // $order->invoice_number = CompanyData::getInvoiceNumber($order->company_id, $order->created_at ?? now());
        $order->invoice_number = date('YmdHis');
        $order->save();

        return $next($order);
    }
}
