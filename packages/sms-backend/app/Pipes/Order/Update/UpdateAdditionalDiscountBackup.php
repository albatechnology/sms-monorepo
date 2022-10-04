<?php

namespace App\Pipes\Order\Update;

use App\Enums\OrderApprovalStatus;
use App\Models\Order;
use Closure;

/**
 * Class UpdateAdditionalDiscount
 * @package App\Pipes\Order\Update
 */
class UpdateAdditionalDiscount
{
    public function handle(Order $order, Closure $next)
    {
        $additional_discount = (int) request()->additional_discount ?? 0;
        $order->approved_by = null;

        if (isset(request()->discount_take_over_by) && request()->discount_take_over_by != null) {
            $order->additional_discount = $additional_discount;
            return $next($this->updateApprovalDiscount($order));
        }

        if ($additional_discount == 0) {
            $order->approval_status = OrderApprovalStatus::NOT_REQUIRED();
            $order->approved_by = null;
            $order->approval_send_to = null;
            $order->additional_discount = 0;
            $order->additional_discount_ratio = null;
            return $next($order);
        }

        if (isset(request()->discount_type) && request()->discount_type == 1) {
            $additional_discount = ($additional_discount / 100) * $order->total_price;
            $order->total_price -= $additional_discount;
        } else {
            $order->total_price -= $additional_discount;
        }

        $order->discount_take_over_by = auth()->id();
        $order->additional_discount = $additional_discount;
        $order->approval_send_to = 3; // send to supervisor
        $order->approval_status = OrderApprovalStatus::WAITING_APPROVAL();
        $order->additional_discount_ratio = $this->calculateOrderAdditionalDiscountRatio($order);

        return $next($order);
    }

    public function updateApprovalDiscount(Order $order)
    {
        $user = auth()->user();
        if (isset(request()->discount_type) && request()->discount_type == 1) {
            $order->additional_discount = ($order->additional_discount / 100) * $order->total_price;
            $order->total_price -= $order->additional_discount;
        } else {
            $order->total_price -= $order->additional_discount;
        }

        if ($user->is_supervisor && ($order->additional_discount <= $user->checkLimitApproval($order->total_price))) {
            $order->approved_by = $user->id;
            $order->approval_status = OrderApprovalStatus::APPROVED();
        } else {
            $order->approval_status = OrderApprovalStatus::WAITING_APPROVAL();
        }

        $order->approval_note = request()->approval_note ?? null;
        $order->approval_send_to = $user->supervisor_type_id == 1 ? 3 : 4; // send to supervisor or director
        $order->additional_discount_ratio = $order->additional_discount == 0 ? null : $this->calculateOrderAdditionalDiscountRatio($order);
        return $order;
    }

    public function calculateOrderAdditionalDiscountRatio(Order $order): ?int
    {
        if (is_null($order->additional_discount)) {
            return null;
        }

        // get ratio as percentage
        $ratio = ($order->total_price + $order->additional_discount) * $order->additional_discount == 0 ? 0 : round(100 / ($order->total_price + $order->additional_discount) * $order->additional_discount);

        if ($ratio === $order->additional_discount_ratio) {
            return $ratio;
        }

        $order->additional_discount_ratio = (int) $ratio;

        return $ratio;
    }
}
