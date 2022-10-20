<?php

namespace App\Pipes\Order\Admin\CartDemand;

use App\Models\Order;
use App\Models\OrderDetail;
use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Save order, order detail, and activity
 *
 * Class SaveOrder
 * @package App\Pipes\Order\Admin
 */
class SaveOrder
{
    public function handle(Order $order, Closure $next)
    {
        $order = DB::transaction(function () use ($order) {
            $details = $order->order_details;
            unset($order->order_details);
            unset($order->discount);
            unset($order->allowed_product_ids);

            $order->save();
            $order_id = $order->id;
            collect($details)->each(function (OrderDetail $detail) use($order_id) {
                unset($detail->discount);
                unset($detail->discount_id);
                $detail->order_id = $order_id;
                $detail->save();
            });

            // $cartDemand = CartDemand::where('user_id', $order->user_id)->whereNotNull('items')->whereNotOrdered()->first();
            // if ($cartDemand) $cartDemand->update(['created_at' => $order->created_at]);

            return $order;
        });

        return $next($order);
    }
}
