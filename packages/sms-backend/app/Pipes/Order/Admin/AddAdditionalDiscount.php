<?php

namespace App\Pipes\Order\Admin;

use App\Enums\OrderApprovalStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Closure;

/**
 * Class AddAdditionalDiscount
 * @package App\Pipes\Order
 */
class AddAdditionalDiscount
{
    public function handle(Order $order, Closure $next)
    {
        if (empty($order->additional_discount)) {
            return $next($order);
        }

        if (isset($order->raw_source['discount_type']) && $order->raw_source['discount_type'] == 1) {
            $additional_discount = 0;
            $additional_discount = ($order->additional_discount / 100) * $order->total_price;
            $order->additional_discount = $additional_discount;
            $order->total_price -= $additional_discount;
        } else {
            $order->total_price -= $order->additional_discount;
        }

        // will require approval unless explicitly stated otherwise
        if (isset($order->raw_source['require_approval']) && $order->raw_source['require_approval'] === false) {
            return $next($order);
        }

        $user = User::findOrFail(request()->user_id);

        $order->discount_take_over_by = $user->id;
        $order->approval_send_to = 3; // send to user type supervisor
        $order->approval_status = OrderApprovalStatus::WAITING_APPROVAL();
        $order->additional_discount_ratio = app(OrderService::class)->calculateOrderAdditionalDiscountRatio($order);

        $productBrandIds = $order->raw_source['product_brand_ids'];
        $storeLeader = $user->supervisor;

        if (count($productBrandIds) > 1) {
            $additional_discount_ratio = $order->additional_discount_ratio;
            $storeLeaderLimitApprovals = $storeLeader->supervisorApprovalLimits->whereIn('product_brand_id', $order->raw_source['product_brand_ids'])->filter(function ($data) use ($additional_discount_ratio) {
                return $data->limit >= $additional_discount_ratio;
            });

            if(count($storeLeaderLimitApprovals) > 0){
                $approval_supervisor_type_id = 1;
            } else {
                $approval_supervisor_type_id = 2;
            }
        } else {
            $productBrandId = $productBrandIds[0];

            if(isset($productBrandId) && $productBrandId != null && $productBrandId != ''){
                $storeLeaderLimitApproval = $storeLeader->supervisorApprovalLimits->first(fn ($data) => $data->product_brand_id == $productBrandId);
                $approval_supervisor_type_id = 1;

                if ($order->additional_discount_ratio > $storeLeaderLimitApproval->limit) {
                    $approval_supervisor_type_id = 2;
                }
            }
            $approval_supervisor_type_id = 2;
        }

        $order->approval_supervisor_type_id = $approval_supervisor_type_id;

        return $next($order);
    }
}
