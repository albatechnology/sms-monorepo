<?php

namespace App\Pipes\Order;

use App\Enums\NotificationType;
use App\Enums\OrderApprovalStatus;
use App\Events\SendExpoNotification;
use App\Models\Order;
use App\Models\User;
use Closure;

/**
 * Class CheckExpectedOrderPrice
 * @package App\Pipes\Order
 */
class SendDiscountApprovalNotification
{
    public function handle(Order $order, Closure $next)
    {
        if ($order->approval_status != OrderApprovalStatus::WAITING_APPROVAL()) return $next($order);

        $type = NotificationType::DiscountApproval();
        $link = config("notification-link.{$type->key}") ?? 'no-link';

        $sales = User::where('id', $order->user_id)->first();
        $getSupervisor = User::where('id', $sales->supervisor_id)->first();

        if ($getSupervisor) {
            $user = $getSupervisor;

            if ($user->supervisor_type_id == 1 && (isset($user->notificationDevices) && count($user->notificationDevices) > 0)) {
                // store leader
                $approvalLimit = $user->supervisorType->discount_approval_limit_percentage;

                if ($order->additional_discount_ratio > $approvalLimit) {
                    // get BUM
                    $user = User::where('id', $getSupervisor->supervisor_id)->first();
                }
            } else {
                // get BUM
                $user = User::where('id', $getSupervisor->supervisor_id)->first();
            }

            if (isset($user->notificationDevices) && count($user->notificationDevices) > 0) {
                SendExpoNotification::dispatch([
                    'receipents' => $user,
                    'badge_for' => $user,
                    'title' => $sales->name . " from " . $sales->channel->name . " Has Request a New Approval",
                    'body' => $sales->name .  ' has request a new discount approval of ' . number_format($order->additional_discount) . ' on invoice ' . $order->invoice_number,
                    'code' => $type->key,
                    'link' => $link,
                ]);
            }
        }

        return $next($order);
    }
}
