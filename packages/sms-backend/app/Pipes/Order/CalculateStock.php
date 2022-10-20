<?php

namespace App\Pipes\Order;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Stock;
use App\Services\StockService;
use Closure;

/**
 * Calculate product unit stock by company_id and channel_id
 *
 * Class CalculateStock
 * @package App\Pipes\Order
 */
class CalculateStock
{
    public function handle(Order $order, Closure $next)
    {
        $channel_id = $order->channel_id;
        $company_id = $order->company_id;

        collect($order->order_details)->each(function (OrderDetail $detail) use ($company_id, $channel_id) {
            $stock = Stock::where('company_id', $company_id)
                ->where('channel_id', $channel_id)
                ->where('product_id', $detail->product_id)
                ->first();

            $outstandingOrder = StockService::outstandingOrder($company_id, $channel_id, $detail->product_id);
            $outstandingShipment = StockService::outstandingShipment($company_id, $channel_id, $detail->product_id);

            // order qty dihapus karena sudah ke cover di outstanding order
            // $incomingIndent = $stock->stock - ($detail->quantity + $outstandingOrder + $outstandingShipment);
            $incomingIndent = $stock->stock - ($outstandingOrder + $outstandingShipment);

            if ($incomingIndent < 0) {
                $incomingIndent = abs($incomingIndent);
                $stock->addIndent($stock->indent + $incomingIndent);
                $stock->addTotalStock($stock->total_stock + $incomingIndent);
            }
        });
        return $next($order);
    }
}
