<?php

namespace App\Services;

use App\Contracts\ExceptionMessage;
use App\Enums\DiscountScope;
use App\Enums\DiscountType;
use App\Enums\OrderApprovalStatus;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\InvalidOrderCancellationException;
use App\Interfaces\Discountable;
use App\Interfaces\DiscountableBase;
use App\Interfaces\DiscountableLine;
use App\Interfaces\Voucherable;
use App\Models\CustomerDiscountUse;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\User;
use App\Models\Voucher;
use App\Pipes\Discountable\CalculateDiscountCascadeForDiscountableLine;
use App\Pipes\Discountable\CalculateDiscountForDiscountableClass;
use App\Pipes\Discountable\CalculateDiscountForDiscountableLineClass;
use App\Pipes\Discountable\CheckDiscountActive;
use App\Pipes\Discountable\CheckDiscountApplied;
use App\Pipes\Discountable\CheckDiscountMinOrderPrice;
use App\Pipes\Discountable\CheckDiscountUseLimit;
use App\Pipes\Discountable\CheckMaxDiscountLimit;
use App\Pipes\Discountable\SyncSumDiscount;
use App\Pipes\Order\AddAdditionalDiscount;
use App\Pipes\Order\AddAdditionalFees;
use App\Pipes\Order\ApplyDiscount;
use App\Pipes\Order\ApplyVouchers;
use App\Pipes\Order\CalculateCartDemand;
// use App\Pipes\Order\CalculateStock;
use App\Pipes\Order\CheckExpectedOrderPrice;
use App\Pipes\Order\CreateActivity;
use App\Pipes\Order\FillOrderAttributes;
use App\Pipes\Order\FillOrderRecord;
use App\Pipes\Order\MakeOrderLines;
use App\Pipes\Order\ProcessInvoiceNumber;
use App\Pipes\Order\SaveOrder;
use App\Pipes\Order\SendDiscountApprovalNotification;
use App\Pipes\Order\UpdateDiscountUse;
use App\Pipes\Voucherable\CalculateVoucherForVoucherableClass;
use App\Pipes\Voucherable\CheckVoucherActive;
use App\Pipes\Voucherable\CheckVoucherMinOrderPrice;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\MessageBag;

class OrderService
{
    use AuthorizesRequests;

    /**
     * Calculate the discount price for a given discountable class.
     *
     * @param DiscountableBase $discountable
     * @param Discount $discount
     * @return int
     * @throws Exception
     */
    public static function calculateTotalDiscount(DiscountableBase $discountable, Discount $discount): int
    {
        if (!$discount->type->in([DiscountType::PERCENTAGE, DiscountType::NOMINAL])) {
            throw new Exception('Unknown discount type is being handled at calculateTotalDiscount method.');
        }

        if ($discount->type->is(DiscountType::PERCENTAGE)) {
            $value = $discountable->getTotalPrice() * $discount->value / 100;
        }

        if ($discount->type->is(DiscountType::NOMINAL)) {

            // if ($discount->scope->in([DiscountScope::QUANTITY, DiscountScope::CATEGORY])) {
            if ($discount->scope->is(DiscountScope::QUANTITY)) {
                // dont let discount to be greater than the total price
                $value = min($discount->value * $discountable->getQuantity(), $discountable->getTotalPrice());
            }

            if ($discount->scope->in([DiscountScope::TYPE, DiscountScope::TRANSACTION])) {
                // dont let discount to be greater than the total price
                $value = min($discount->value, $discountable->getTotalPrice());
            }
        }

        return $value ?? 0;
    }

    /**
     * Calculate the Voucher price for a given Voucherable class.
     *
     * @param Voucherable $voucherable
     * @param Voucher $voucher
     * @return int
     * @throws Exception
     */
    public static function calculateTotalVoucher(Voucherable $voucherable, Voucher $voucher, bool $setSumTotalVoucher = true): int
    {
        if ($setSumTotalVoucher) {
            $value = $voucherable->getTotalVoucher() + $voucher->value;
        } else {
            $value = min($voucher->value, $voucherable->getTotalPriceVoucher());
        }

        return $value ?? 0;
    }

    /**
     * Set a discount to a discountable model
     *
     * @param Discountable $discountable
     * @param Discount $discount
     */
    public static function setDiscount(Discountable $discountable, Discount $discount)
    {
        // set discount, before it is ready to be processed in pipeline
        $discountable->discount_id = $discount->id;
        $discountable->discount    = $discount;

        app(Pipeline::class)
            ->send($discountable)
            ->through([
                CheckDiscountActive::class,
                CheckDiscountMinOrderPrice::class,
                CheckDiscountUseLimit::class,
                CalculateDiscountForDiscountableClass::class,
                CalculateDiscountForDiscountableLineClass::class,
                // SyncSumDiscount::class,
                CheckMaxDiscountLimit::class,
                CheckDiscountApplied::class,
                CalculateDiscountCascadeForDiscountableLine::class,
            ])
            ->thenReturn();
    }

    /**
     * Set a Voucher to a Voucherable model
     *
     * @param Voucherable $voucherable
     * @param Voucher $voucher
     */
    public static function setVoucher(Voucherable $voucherable, Voucher $voucher)
    {
        // set Voucher, before it is ready to be processed in pipeline
        // $voucherable->voucher_id = $voucher->id;
        $voucherable->voucher    = $voucher;

        app(Pipeline::class)
            ->send($voucherable)
            ->through([
                CheckVoucherActive::class,
                CheckVoucherMinOrderPrice::class,
                // CheckVoucherUseLimit::class,
                CalculateVoucherForVoucherableClass::class,
                // CalculateVoucherForVoucherableLineClass::class,
                // SyncSumVoucher::class,
                // CheckMaxVoucherLimit::class,
                // CheckVoucherApplied::class,
                // CalculateVoucherCascadeForVoucherableLine::class,
            ])
            ->thenReturn();
    }

    /**
     * Record the discount used into the customer count.
     * @param Discountable $discountable
     * @throws Exception
     */
    public static function recordDiscountUse(Discountable $discountable)
    {
        if (!$discount = $discountable->getDiscount()) {
            throw new Exception(ExceptionMessage::DiscountableNeedDiscount);
        }

        if (!$customer_id = $discountable->getCustomerId()) {
            throw new Exception(ExceptionMessage::DiscountableNeedCustomer);
        }

        if (!$discountable_id = $discountable->getId()) {
            throw new Exception(ExceptionMessage::DiscountableNeedId);
        }

        $customerUse = CustomerDiscountUse::firstOrCreate(
            [
                'customer_id' => $customer_id,
                'discount_id' => $discount->id,
            ]
        );

        // add the new order id
        $discountable_ids = collect($customerUse->order_ids)->push($discountable_id)->all();
        $customerUse->update(['order_ids' => $discountable_ids]);

        // add to the counter
        CustomerDiscountUse::where('id', $customerUse->id)
            ->increment('use_count');
    }

    /**
     * Record the discounts used into the customer count.
     * @param Discountable $discountable
     * @throws Exception
     */
    public static function recordDiscountsUse(Discountable $discountable)
    {
        if (!$discounts = $discountable->getOrderDiscounts()) {
            throw new Exception(ExceptionMessage::DiscountableNeedDiscount);
        }

        if (!$customer_id = $discountable->getCustomerId()) {
            throw new Exception(ExceptionMessage::DiscountableNeedCustomer);
        }

        if (!$discountable_id = $discountable->getId()) {
            throw new Exception(ExceptionMessage::DiscountableNeedId);
        }

        foreach ($discounts as $discount) {
            $customerUse = CustomerDiscountUse::firstOrCreate(
                [
                    'customer_id' => $customer_id,
                    'discount_id' => $discount->discount_id,
                ]
            );

            // add the new order id
            $discountable_ids = collect($customerUse->order_ids)->push($discountable_id)->all();
            $customerUse->update(['order_ids' => $discountable_ids]);

            // add to the counter
            CustomerDiscountUse::where('id', $customerUse->id)->increment('use_count');
        }
    }

    /**
     * Process order from raw source
     *
     * @param Order $order
     * @return Order
     */
    public static function processOrder(Order $order): Order
    {
        return app(Pipeline::class)
            ->send($order)
            ->through(
                [
                    // transfer attributes from raw_source to properties
                    FillOrderAttributes::class,
                    FillOrderRecord::class,

                    // make order lines
                    MakeOrderLines::class,

                    // apply discount
                    ApplyDiscount::class,
                    // ApplyVouchers::class,
                    // calculateCartDemand
                    // CalculateCartDemand::class,
                    AddAdditionalDiscount::class,

                    // add shipping and packing fee
                    AddAdditionalFees::class,

                    // validate whether the final price match with the expected price
                    // may differ if suddenly discount is expired or modified
                    CheckExpectedOrderPrice::class,

                    // save the order and order details
                    SaveOrder::class,

                    // record discount use (if used)
                    UpdateDiscountUse::class,

                    // create activity against this order for the sales user
                    CreateActivity::class,

                    // generate unique invoice number
                    ProcessInvoiceNumber::class,

                    // generate unique invoice number
                    SendDiscountApprovalNotification::class,

                    // calculate stock per product unit
                    // CalculateStock::class
                ]
            )
            ->thenReturn();
    }

    /**
     * Change order status to shipment and attempt to fulfil
     * order from existing stock.
     *
     * @param Order $order
     */
    public static function setOrderStatusToShipment(Order $order): void
    {
        $order->status = OrderStatus::SHIPMENT();
        $order->save();
        StockService::fulfillOrder($order);
    }

    /**
     * Evaluate order and order detail shipping status
     *
     * @param Order $order
     */
    public static function evaluateShippingStatus(Order $order): void
    {
        $order->order_details->each(function (OrderDetail $detail) {
            $detail->refreshShipmentStatus();
        });

        $order->refreshShipmentStatus();
    }

    /**
     * TODO: we may want to save this as an attribute on the order.
     * @param Order $order
     * @param PaymentStatus|null $paymentStatus
     * @return int
     */
    public static function getTotalPaymentAmount(Order $order, PaymentStatus $paymentStatus = null): int
    {
        $query = $order->orderPayments();

        if ($paymentStatus) {
            $query = $query->where('status', $paymentStatus->value);
        }

        return $query->sum('amount') ?? 0;
    }

    /**
     * Make a payment against an order
     *
     * @param int $amount
     * @param int $payment_type_id
     * @param int $order_id
     * @param string|null $reference
     * @param PaymentStatus|null $status
     * @param User|null $user
     * @return MessageBag|Payment
     */
    public function makeOrderPayment(
        int $amount,
        int $payment_type_id,
        int $order_id,
        ?string $reference = null,
        PaymentStatus $status = null,
        User $user = null,
    ): MessageBag|Payment {
        // extra validation, check that payment type has the same
        $order       = Order::findOrFail($order_id);
        // $paymentType = PaymentType::findOrFail($payment_type_id);

        // if ($order->company_id != $paymentType->company_id) {
        //     return new MessageBag([
        //         'payment_type_id' => ['Invalid payment type for this order.']
        //     ]);
        // }

        if ($order->approval_status->is(OrderApprovalStatus::WAITING_APPROVAL)) {
            return new MessageBag([
                'order_id' => ['Unable to make payment, order awaiting supervisor approval.']
            ]);
        }

        $payment = Payment::make(
            [
                'amount'          => $amount,
                'reference'       => $reference,
                'status'          => $status ?? PaymentStatus::PENDING(),
                'payment_type_id' => $payment_type_id,
                'added_by_id'     => $user->id ?? user()->id,
                'order_id'        => $order_id,
                'channel_id'        => $order->channel_id,
                'subscribtion_user_id' => $order->subscribtion_user_id,
                // 'company_id'      => $order->company_id,
            ]
        );

        $payment->save();

        return $payment;
    }

    /**
     * Set the cascaded discount value to the order detail of an order.
     * Cascaded detail apply when there is discount at the order level.
     * @param Discountable $discountable
     * @param bool $save whether the update should be saved against the model immediately
     * @return Discountable
     */
    public function setOrderDetailCascadedDiscount(Discountable $discountable, $save = false): Discountable
    {
        $mainDiscount = 0;

        if (($discount = $discountable->getDiscount()) && $discount->scope->isNot(DiscountScope::TRANSACTION)) {
            $mainDiscount = $discountable->getTotalDiscount();
        }

        // calculate the discount percentage
        $totalDiscountIncludingAdditionalDiscount = $mainDiscount + $discountable->getAdditionalDiscount();

        if ($totalDiscountIncludingAdditionalDiscount === 0) {
            $discountedPercentage = 0;
        } else {
            $discountedPercentage = $totalDiscountIncludingAdditionalDiscount / ($discountable->getTotalPrice() + $totalDiscountIncludingAdditionalDiscount);
        }

        $allowedProductIds = $discountable->allowed_product_ids ?? [];
        $discountable->getDiscountableLines()->each(function (DiscountableLine $line) use ($discountedPercentage, $allowedProductIds, $save) {
            if (in_array($line->product_id, $allowedProductIds)) {
                $line->setTotalCascadedDiscount((int)round($line->getTotalPrice() * $discountedPercentage));

                if ($save) {
                    $line->save();
                }
            }
        });

        return $discountable;
    }

    /**
     * @param Order $order
     * @param User $user
     * @return Order
     * @throws Exception
     */
    public function approveOrder(Order $order, User $user, array $params = []): Order
    {
        if ($order->approval_status->isNot(OrderApprovalStatus::WAITING_APPROVAL())) {
            return $order;
            // throw new Exception("Order {$order->id} is not waiting for approval");
        }

        $canApprove = Order::query()
            ->waitingApproval()
            ->canBeApprovedBy($user)
            ->approvalSendToMe()
            ->where('id', $order->id)
            ->exists();

        if (!$canApprove) {
            throw new Exception("User {$user->id} does not have permission to approve order {$order->id}");
        }

        $dataToUpdate = [];
        $dataToUpdate['approval_status'] = OrderApprovalStatus::APPROVED;
        $dataToUpdate['approved_by'] = $user->id;
        $dataToUpdate['approval_send_to'] = null;
        $dataToUpdate['approval_supervisor_type_id'] = null;

        if (count($params) > 0) {
            if (isset($params['comment']) && $params['comment'] != '') {
                $order->activity->comments()->create([
                    'user_id' => $user->id,
                    'content' => $params['comment'],
                ]);
            }

            if (isset($params['reject']) && $params['reject'] == true) {
                $sales = User::where('id', $order->user_id)->first();
                if ($sales->notificationDevices && count($sales->notificationDevices) > 0) {
                    $type = \App\Enums\NotificationType::ActivityReminder();
                    $link = sprintf(config("notification-link.{$type->key}"), $order->activity->id);
                    \App\Events\SendExpoNotification::dispatch([
                        'receipents' => $sales,
                        'badge_for' => $sales,
                        'title' => $user->name . " Rejected Your Additional Discount",
                        'body' => $user->name .  ' rejected your additional discount on invoice ' . $order->invoice_number,
                        'code' => $type->key,
                        'link' => $link,
                    ]);
                }

                $dataToUpdate['approval_status'] = OrderApprovalStatus::REJECTED;
                $dataToUpdate['approved_by'] = $user->id;
                if ($user->is_director) {
                    $dataToUpdate['approval_send_to'] = 3;
                    $dataToUpdate['approval_supervisor_type_id'] = 2;
                }
            }
        }

        return tap($order)->update($dataToUpdate);
    }

    /**
     * Takes an existing order and return a new, unprocessed copy of the given order.
     * The new order will not be committed to the database yet.
     * @param Order $order
     * @param array $attributes override clone attribute
     * @return Order
     */
    public function cloneOrder(Order $order, array $attributes = []): Order
    {
        $rawSource = $order->raw_source;

        // remove expected price from old order
        $rawSource['expected_price'] = null;

        $rawSource = array_merge($rawSource, $attributes);

        return Order::make(
            [
                'raw_source' => $rawSource,
                'user_id' => $order->user_id,
            ]
        );
    }



    /**
     * @param Order $order
     * @return Order
     * @throws Exception
     */
    public function cancelOrder(Order $order): Order
    {
        if ($order->status->isNot(OrderStatus::QUOTATION) || !is_null($order->deal_at)) {
            throw new InvalidOrderCancellationException();
        }

        $order->update(['status' => OrderStatus::CANCELLED]);

        return $order;
    }

    /**
     * Calculate the percentage of the additional discount in
     * relation to the total price of an order.
     * @param Order $order
     * @param bool $update calculate and also update the additional_discount_ratio of the given model
     * @return int|null
     */
    public function calculateOrderAdditionalDiscountRatio(Order $order, bool $update = false): ?int
    {
        if (is_null($order->additional_discount)) {
            return null;
        }

        // get ratio as percentage
        $ratio = ($order->total_price + $order->additional_discount) * $order->additional_discount == 0 ? 0 : round(100 / ($order->total_price + $order->additional_discount) * $order->additional_discount);

        if ($ratio === $order->additional_discount_ratio) {
            return $ratio;
        }

        $order->update(['additional_discount_ratio' => $ratio]);

        return $ratio;
    }

    /**
     * Calculate the order payment status
     * @param int $amountPaid
     * @param int $totalPrice
     * @param int $paymentCount
     * @return OrderPaymentStatus
     */
    public function calculateOrderPaymentStatus(int $amountPaid, int $totalPrice, int $paymentCount): OrderPaymentStatus
    {
        if ($amountPaid > $totalPrice) {
            $status = OrderPaymentStatus::OVERPAYMENT();
        } elseif ($amountPaid === $totalPrice) {
            $status = OrderPaymentStatus::SETTLEMENT();
        } elseif ($amountPaid === 0 && $paymentCount === 0) {
            $status        = OrderPaymentStatus::NONE();
        } elseif ($amountPaid === 0 && $paymentCount !== 0) {
            $status        = OrderPaymentStatus::REFUNDED();
        } elseif ($amountPaid >= $totalPrice * 50 / 100) {
            // 50% payment considered as down payment
            $status = OrderPaymentStatus::DOWN_PAYMENT();
        } else {
            $status        = OrderPaymentStatus::PARTIAL();
        }

        return $status;
    }

    public static function calculateCartDemand(int $user_id, int $order_id = null): int
    {
        $cartDemand = \App\Models\CartDemand::where('user_id', $user_id)->whereNotNull('items');

        if ($order_id) {
            $cartDemand->where('order_id', $order_id);
        } else {
            $cartDemand->whereNotOrdered();
        }

        $cartDemand = $cartDemand->first();

        if ($cartDemand) return (int) $cartDemand->total_price;

        return 0;
    }
}
