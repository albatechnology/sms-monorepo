<?php

namespace App\Models;

use App\Enums\DiscountError;
use App\Enums\OrderApprovalStatus;
use App\Enums\OrderDetailShipmentStatus;
use App\Enums\OrderDetailStatus;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderShipmentStatus;
use App\Enums\OrderStatus;
use App\Enums\OrderStockStatus;
use App\Enums\PaymentStatus;
use App\Events\OrderCancelled;
use App\Events\OrderCreated;
use App\Events\OrderDealReversal;
use App\Events\OrderIsDeal;
use App\Events\OrderPaymentDownPayment;
use App\Events\OrderPaymentSettlement;
use App\Interfaces\Discountable;
use App\Interfaces\Reportable;
use App\Interfaces\Tenanted;
use App\Services\OrderService;
use App\Traits\Auditable;
use App\Traits\IsCompanyTenanted;
use App\Traits\IsDiscountable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperOrder
 */
class OrderBackup extends BaseModel implements Tenanted, Discountable, Reportable
{
    use SoftDeletes, Auditable, IsCompanyTenanted, IsDiscountable;

    public $table = 'orders';

    public ?int $expected_price = null;

    protected $fillable = [
        'raw_source',
        'note',
        'channel_id',
        'interior_design_id',
        'discount_error',
        'payment_status',
        'approval_status',
        'status',
        'stock_status',
        'shipment_status',
        'deal_at',
        'additional_discount_ratio',
        'approved_by',
        'discount_take_over_by',
        'approval_send_to',
        'quotation_valid_until_datetime',
        'created_at',
        'approval_note',
    ];

    protected $dates = [
        'expected_shipping_datetime',
        'quotation_valid_until_datetime',
        'deal_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'approved_by'               => 'integer',
        'total_discount'            => 'integer',
        'total_price'               => 'integer',
        'user_id'                   => 'integer',
        'lead_id'                   => 'integer',
        'customer_id'               => 'integer',
        'company_id'                => 'integer',
        'channel_id'                => 'integer',
        'interior_design_id'        => 'integer',
        'activity_id'               => 'integer',
        'tax_invoice_sent'          => 'integer',
        'additional_discount'       => 'integer',
        'shipping_fee'              => 'integer',
        'packing_fee'               => 'integer',
        'amount_paid'               => 'integer',
        'additional_discount_ratio' => 'integer',
        'discount_take_over_by' => 'integer',
        'records'                   => 'json',
        'raw_source'                => 'json',
        'discount_error'            => DiscountError::class,
        'payment_status'            => OrderPaymentStatus::class,
        'status'                    => OrderStatus::class,
        'approval_status'           => OrderApprovalStatus::class,
        'stock_status'              => OrderStockStatus::class,
        'shipment_status'           => OrderShipmentStatus::class,
        'approval_send_to'          => \App\Enums\UserType::class,
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::created(function (self $model) {
            OrderCreated::dispatch($model);
        });

        self::saved(function (self $model) {
            if ($model->propertyChanged('status') && $model->status->is(OrderStatus::CANCELLED)) {
                OrderCancelled::dispatch($model);
            }

            if ($model->propertyChanged('approval_status') && $model->approval_status->is(OrderApprovalStatus::APPROVED)) {
                $type = \App\Enums\NotificationType::DiscountApproval();
                $link = config("notification-link.{$type->key}") ?? 'no-link';

                if ($model->user->notificationDevices && count($model->user->notificationDevices) > 0) {
                    \App\Events\SendExpoNotification::dispatch([
                        'receipents' => $model->user,
                        'badge_for' => $model->user,
                        'title' => "Your Request Has Been Proceed",
                        'body' => "Your request on " . $model->invoice_number . " has been proceed by " . $model->approvedBy->name,
                        'code' => $type->key,
                        'link' => $link,
                    ]);
                }
            }
        });

        self::updated(function (self $model) {
            if ($model->propertyChanged('deal_at')) {

                is_null($model->deal_at) ? OrderDealReversal::dispatch($model) : OrderIsDeal::dispatch($model);
            }
        });

        parent::boot();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function interiorDesign(): BelongsTo
    {
        return $this->belongsTo(InteriorDesign::class);
    }

    public function orderOrderTrackings(): HasMany
    {
        return $this->hasMany(OrderTracking::class, 'order_id', 'id');
    }

    public function orderOrderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function order_details(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function orderShipments()
    {
        return $this->hasMany(Shipment::class, 'order_id', 'id');
    }

    public function ordersTargets(): BelongsToMany
    {
        return $this->belongsToMany(Target::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function discountTakeOverBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'discount_take_over_by');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function scopeCustomerName($query, $name)
    {
        return $query->whereHas('customer', function ($query) use ($name) {
            return $query->whereNameLike($name);
        });
    }

    public function scopeCustomerNameAndInvoiceNumber($query, $search)
    {
        return $query->whereHas('customer', function ($query) use ($search) {
            return $query->whereNameLike($search);
        })->orWhere('invoice_number', 'LIKE', "%$search%");
    }

    public function billing_address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function shipping_address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function activity()
    {
        return $this->hasOne(Activity::class);
    }

    public function cartDemand(): HasOne
    {
        return $this->hasOne(CartDemand::class, 'order_id');
    }

    public function getDiscountableLines(): ?Collection
    {
        return $this->order_details;
    }

    public function getOriginalPriceAttribute(): int
    {
        return ($this->total_price ?? 0) +
            ($this->total_discount ?? 0) +
            ($this->additional_discount ?? 0) -
            ($this->shipping_fee ?? 0) -
            ($this->packing_fee ?? 0);
    }

    public function getShippingAddressAttribute()
    {
        if ($address = $this->records['shipping_address']) {
            return Address::make($address);
        }

        return null;
    }

    public function getBillingAddressAttribute()
    {
        if ($address = $this->records['billing_address']) {
            return Address::make($address);
        }

        return null;
    }

    public function getCustomerId(): int
    {
        return $this->customer_id;
    }

    public function refreshPaymentStatus(): static
    {
        $originalPaymentStatus = $this->payment_status;
        $payments              = $this->orderPayments()
            ->where('status', PaymentStatus::APPROVED)
            ->get(['id', 'amount']);

        $amountPaid = $payments->sum('amount');

        $this->amount_paid = $amountPaid;

        $status = app(OrderService::class)
            ->calculateOrderPaymentStatus(
                $amountPaid,
                $this->total_price,
                $payments->count()
            );

        if ($status->in([OrderPaymentStatus::NONE, OrderPaymentStatus::DOWN_PAYMENT, OrderPaymentStatus::PARTIAL])) {
            $this->deal_at = null;
        }

        // if status has changed
        if ($originalPaymentStatus->isNot($status)) {
            $this->payment_status = $status;
            $this->save();

            $orderIsQuotation = $this->status->is(OrderStatus::QUOTATION);

            $paymentIsSettlement = $this->payment_status->in([
                OrderPaymentStatus::SETTLEMENT, OrderPaymentStatus::OVERPAYMENT
            ]);

            $paymentIsDownPayment = $this->payment_status->in([
                OrderPaymentStatus::DOWN_PAYMENT
            ]);

            if ($orderIsQuotation && $paymentIsSettlement) {
                OrderPaymentSettlement::dispatch($this);
            }

            if ($orderIsQuotation && $paymentIsDownPayment) {
                OrderPaymentDownPayment::dispatch($this);
            }

            // set as deal
            if ($orderIsQuotation && ($paymentIsDownPayment || $paymentIsSettlement) && is_null($this->deal_at)) {
                $this->deal_at = now();
                $this->save();
            }
        }

        return $this;
    }

    public function orderPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id', 'id');
    }

    public function refreshShipmentStatus(): static
    {
        // terminate early if there is no order detail
        if ($this->order_details->isEmpty()) {
            $this->update(['shipment_status' => OrderShipmentStatus::NONE]);
            return $this;
        }

        $allStatusMapping = [
            OrderDetailShipmentStatus::NONE       => OrderShipmentStatus::NONE,
            OrderDetailShipmentStatus::PREPARING  => OrderShipmentStatus::PREPARING,
            OrderDetailShipmentStatus::DELIVERING => OrderShipmentStatus::DELIVERING,
            OrderDetailShipmentStatus::ARRIVED    => OrderShipmentStatus::ARRIVED,
        ];

        // if all order detail have the same shipment status, use direct mapping
        foreach ($allStatusMapping as $orderDetailShipmentStatus => $orderShipmentStatus) {
            $hasAllStatus = $this->order_details->every(function (OrderDetail $detail) use ($orderDetailShipmentStatus) {
                return $detail->shipment_status->is($orderDetailShipmentStatus);
            });

            if ($hasAllStatus) {
                $this->update(['shipment_status' => $orderShipmentStatus]);
                return $this;
            }
        }

        // otherwise use partial
        $this->update(['shipment_status' => OrderShipmentStatus::PARTIAL()]);
        return $this;
    }


    public function refreshStockStatus(): static
    {
        $allOrderDetailFulfilled = $this->order_details->every(function (OrderDetail $detail) {
            return $detail->status->is(OrderDetailStatus::FULFILLED);
        });

        $this->stock_status = $allOrderDetailFulfilled ? OrderStockStatus::FULFILLED() : OrderStockStatus::INDENT();
        return $this;
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function tax_invoice()
    {
        return $this->belongsTo(TaxInvoice::class, 'tax_invoice_id');
    }

    /**
     * order that needs to be delivered
     */
    public function scopeWhereWaitingDelivery($query)
    {
        return $query->where('status', OrderStatus::SHIPMENT)
            ->where('shipment_status', '<>', OrderShipmentStatus::ARRIVED);
    }

    public function scopeNotCancelled($query)
    {
        return $query->whereNotIn('status', [OrderStatus::CANCELLED]);
    }

    public function scopeNotReturned($query)
    {
        return $query->whereNotIn('status', [OrderStatus::RETURNED]);
    }

    public function scopeCanBeApprovedBy($query, User $user)
    {
        $user = $user ?? user();

        // sales cant approve any
        if ($user->is_sales) {
            return $query->whereIn('id', []);
        }

        // admin (includes director) can approve all. Supervisor can show all discount approvals but is only allowed to approve discount if <= limit
        if ($user->is_admin || ($user->supervisorType && $user->supervisor_type_id == 2)) {
            return $query;
        }

        // supervisor can only approve its descendant's order
        $ids   = $user->getDescendantIds();
        $query = $query->whereIn('user_id', $ids);

        // supervisor have limit to the amount of discount that they
        // can approve based on the type.
        $limit = 0;
        if ($user->supervisorType) {
            $limit = $user->supervisorType->discount_approval_limit_percentage ?? 0;
        }

        $query = $query->where(function ($q) use ($limit) {
            $q->where('additional_discount_ratio', '<=', $limit)
                ->orWhereNull('additional_discount_ratio');
        });

        return $query;
    }

    public function scopeWaitingApproval($query)
    {
        return $query->where('approval_status', OrderApprovalStatus::WAITING_APPROVAL);
    }

    public function scopeRequiredApproval($query)
    {
        return $query->where('approval_status', '!=', OrderApprovalStatus::NOT_REQUIRED);
    }

    public function scopeApprovalSendToMe($query)
    {
        return $query->where('approval_send_to', auth()->user()->type->value);
    }

    public function getCompanyIdAttribute($value)
    {
        return (int)$value;
    }

    /**
     * @return OrderPaymentStatus
     */
    public function getPaymentStatusForInvoice(): OrderPaymentStatus
    {
        // we want total amount even when they are not yet approved
        $payments = $this->orderPayments
            ->filter(function (Payment $payment) {
                // remove payment that has been cancelled
                return $payment->status->isNot(PaymentStatus::REJECTED);
            });

        $totalPaid = $payments->sum('amount');
        $count     = $payments->count();

        return app(OrderService::class)->calculateOrderPaymentStatus($totalPaid, $this->total_price, $count);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Get all target map
     */
    public function targetMaps()
    {
        return $this->morphMany(TargetMap::class, 'model');
    }

    public function getAdditionalDiscount(): int
    {
        return $this->additional_discount ?? 0;
    }

    public function stockTransfers()
    {
        return $this->hasMany(StockTransfer::class);
    }
}
