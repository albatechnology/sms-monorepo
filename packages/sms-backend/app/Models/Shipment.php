<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use App\Events\ShipmentUpdated;
use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperShipment
 */
class Shipment extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'shipments';

    protected $dates = [
        'estimated_delivery_date',
        'arrived_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const STATUS_SELECT = [
        'waiting'    => 'Waiting',
        'dispatched' => 'Dispatched',
        'arrived'    => 'Arrived',
        'cancelled'  => 'Cancelled',
    ];

    protected $casts = [
        'status' => ShipmentStatus::class,
    ];

    protected $fillable = [
        'order_id',
        'fulfilled_by_id',
        'status',
        'note',
        'reference',
        'estimated_delivery_date',
        'arrived_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        self::saving(function (self $model) {
            if ($model->getOriginal('status') != $model->status && ShipmentStatus::ARRIVED()->is($model->status)) {
                $model->arrived_at = now();
            }
        });

        self::updated(function (self $model) {
            if ($model->getOriginal('status') != $model->status) {
                ShipmentUpdated::dispatch($model->refresh());
            }
        });

        parent::boot();
    }

    public function cutStock($data, $originalStatus)
    {
        $allowedCutStockStatus = [ShipmentStatus::ARRIVED, ShipmentStatus::DELIVERING];
        $allowedRestoreStockStatus = [ShipmentStatus::PREPARING, ShipmentStatus::CANCELLED];
        $shipQty = intval($data['quantity']);
        $orderDetail = OrderDetail::find($data['id']);
        $stock = Stock::where('channel_id', $orderDetail->order->channel_id)->where('company_id', $orderDetail->company_id)->where('product_id', $orderDetail->product_id)->first();

        if ($this->status->in($allowedCutStockStatus) && !in_array($originalStatus, $allowedCutStockStatus)) {
            if ($stock->stock >= $shipQty) {
                // available_stock = current_available_stock - barang yang dikirim
                $stock->stock = $stock->stock - $shipQty;
                // outstanding_shipment = current_outstanding_shipment - barang yang dikirim -> SKIP
                // total_stock = current_total_stock - barang yang dikirim
                $stock->total_stock = $stock->total_stock - $shipQty;

                $stock->deductStock($shipQty);

                StockHistory::create(
                    [
                        'stock_id'        => $stock->id,
                        'quantity'        => $shipQty * -1,
                        'type'            => \App\Enums\StockHistoryType::ORDER(),
                        'order_detail_id' => $orderDetail->id,
                        'user_id'         => $orderDetail->order->user_id,
                        'company_id'      => $orderDetail->order->company_id,
                    ]
                );
            }
        }

        if ($this->status->in($allowedRestoreStockStatus) && in_array($originalStatus, $allowedCutStockStatus)) {
            //balikin stock
            $stock->stock = $stock->stock + $shipQty;
            $stock->total_stock = $stock->total_stock + $shipQty;
        }

        $stock->save();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function fulfilled_by()
    {
        return $this->belongsTo(User::class, 'fulfilled_by_id');
    }

    public function orderDetails()
    {
        return $this->belongsToMany(OrderDetail::class)->withPivot('quantity');
    }

    public function orderDetailShipment()
    {
        return $this->hasMany(OrderDetailShipment::class);
    }

    public function getEstimatedDeliveryDateAttribute($value)
    {
        if (!$value) return null;

        // $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        // return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
        return date(config('panel.date_format'), strtotime($value));
    }

    public function setEstimatedDeliveryDateAttribute($value)
    {
        $this->attributes['estimated_delivery_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getArrivedAtAttribute($value)
    {
        if (!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    public function setArrivedAtAttribute($value)
    {
        $this->attributes['arrived_at'] = $value ? Carbon::parse($value) : null;
    }
}
