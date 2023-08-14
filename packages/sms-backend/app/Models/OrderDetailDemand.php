<?php

namespace App\Models;

// use App\Enums\OrderDetailShipmentStatus;
use App\Enums\OrderDetailStatus;
use App\Enums\ShipmentStatus;
use App\Interfaces\DiscountableLine;
use App\Interfaces\Reportable;
use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsDiscountable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperOrderDetailDemand
 */
class OrderDetailDemand extends BaseModel implements HasMedia, DiscountableLine, Reportable
{
    use SoftDeletes, CustomInteractsWithMedia, Auditable, IsDiscountable;

    public $table = 'order_detail_demands';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'photo',
    ];

    protected $fillable = [
        'product_id',
        'records',
        'order_id',
        'product_detail',
        'note',
        'quantity',
        'quantity_fulfilled',
        'unit_price',
        'total_cascaded_discount',
        // 'shipment_status',
        'status',
        'price',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'quantity'                => 'integer',
        'quantity_fulfilled'      => 'integer',
        'unit_price'              => 'integer',
        'total_discount'          => 'integer',
        'total_cascaded_discount' => 'integer',
        'total_price'             => 'integer',
        'order_id'                => 'integer',
        'company_id'              => 'integer',
        'records'                 => 'json',
        'status'                  => OrderDetailStatus::class,
        // 'shipment_status'         => OrderDetailShipmentStatus::class,
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function getPhotoAttribute()
    {
        $files = $this->getMedia('photo');
        $files->each(function ($item) {
            $item->url       = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview   = $item->getUrl('preview');
        });

        return $files;
    }

    public function orderDetailsTargets()
    {
        return $this->belongsToMany(Target::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function shipments()
    {
        return $this->belongsToMany(Shipment::class)->withPivot('quantity');
    }

    public function getProductId(): int
    {
        return $this->product_id;
    }

    public function refreshStatus(): static
    {
        if ($this->quantity_fulfilled <= 0) {
            $this->status = OrderDetailStatus::NOT_FULFILLED();
        } elseif ($this->quantity_fulfilled === $this->quantity) {
            $this->status = OrderDetailStatus::FULFILLED();
        } elseif ($this->quantity_fulfilled < $this->quantity) {
            $this->status = OrderDetailStatus::PARTIALLY_FULFILLED();
        } else {
            $this->status = OrderDetailStatus::OVER_FULFILLED();
        }

        return $this;
    }

    public function getIndentAttribute()
    {
        return $this->quantity - $this->quantity_fulfilled;
    }

    /**
     * Sum the shipment quantity of this order detail
     * @param ?ShipmentStatus $status filter by status
     * @return int
     */
    public function sumShipmentQuantity(?ShipmentStatus $status = null): int
    {
        $collection = $this->shipments;

        if ($this->shipments->isEmpty()) {
            return 0;
        }

        if ($status) {
            $collection = $collection->filter(function (Shipment $shipment) use ($status) {
                return $shipment->status->is($status);
            });
        }

        return $collection->sum('pivot.quantity') ?? 0;
    }

    public function refreshShipmentStatus(): self
    {
        return $this;
        // // check for no shipment
        // if ($this->shipments->isEmpty()) {
        //     $this->update(['shipment_status' => OrderDetailShipmentStatus::NONE]);
        //     return $this;
        // }

        // // if shipping 0 quantity
        // if ($this->sumShippingQuantity() === 0) {
        //     $this->update(['shipment_status' => OrderDetailShipmentStatus::NONE]);
        //     return $this;
        // }

        // // if shipping partial quantity
        // if ($this->sumShippingQuantity() !== $this->quantity) {
        //     $this->update(['shipment_status' => OrderDetailShipmentStatus::PARTIAL]);
        //     return $this;
        // }

        // // for an order detail to not be partial, the whole of the quantity
        // // must be under the same status. For example. order detail quantity 2
        // // have 2 shipment, both shipment on status delivering, then order detail
        // // status would be delivering as well.
        // $allStatusMapping = [
        //     ShipmentStatus::PREPARING  => OrderDetailShipmentStatus::PREPARING,
        //     ShipmentStatus::DELIVERING => OrderDetailShipmentStatus::DELIVERING,
        //     ShipmentStatus::ARRIVED    => OrderDetailShipmentStatus::ARRIVED,
        //     ShipmentStatus::CANCELLED  => OrderDetailShipmentStatus::NONE,
        // ];

        // foreach ($allStatusMapping as $shipmentStatus => $orderDetailShipmentStatus) {

        //     $sumByStatus = $this->shipments
        //         ->filter(function (Shipment $shipment) use ($shipmentStatus) {
        //             return $shipment->status->is($shipmentStatus);
        //         })
        //         ->sum('pivot.quantity');

        //     if ($sumByStatus === $this->quantity) {
        //         $this->update(['shipment_status' => $orderDetailShipmentStatus]);
        //         return $this;
        //     }
        // }

        // // for everything else set partial (we have partial statuses)
        // $this->update(['shipment_status' => OrderDetailShipmentStatus::PARTIAL]);
        // return $this;
    }

    /**
     * Sum the shipping quantity of this order detail (NOT COUNTING CANCELLED)
     * @return int
     */
    public function sumShippingQuantity(): int
    {
        $collection = $this->shipments;

        if ($this->shipments->isEmpty()) {
            return 0;
        }

        $collection = $collection->filter(function (Shipment $shipment) {
            return $shipment->status->isNot(ShipmentStatus::CANCELLED());
        });

        return $collection->sum('pivot.quantity') ?? 0;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function setTotalCascadedDiscount(int $price)
    {
        $this->total_cascaded_discount = $price;
    }

    /**
     * Get brand from records
     */
    public function getBrandRecord(): array
    {
        return $this->records['product']['brand'] ?? [];
    }

    /**
     * Report price is the total price of this order detail,
     * accounting for cascaded discount from order as well.
     */
    public function getReportPrice(): int
    {
        return $this->total_price - $this->total_cascaded_discount;
    }
}
