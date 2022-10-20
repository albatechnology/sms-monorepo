<?php

namespace App\Models;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Exceptions\InsufficientStockException;
use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\IsCompanyTenanted;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;

/**
 * @mixin IdeHelperStock
 */
class Stock extends BaseModel implements Tenanted
{
    use Auditable, IsCompanyTenanted;

    public $table = 'stocks';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    protected $casts = [
        'stock'           => 'integer',
        'indent'           => 'integer',
        'total_stock'           => 'integer',
        'location_id'      => 'integer',
        'product_id' => 'integer',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function stockFromStockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'stock_from_id', 'id');
    }

    public function stockToStockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'stock_to_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @param int $value
     * @throws InsufficientStockException
     */
    public function addStockNew(int $value, $cut_indent = false)
    {
        if ($this->stock + $value < 0) {
            throw new InsufficientStockException();
        }

        if ($cut_indent) {
            $incomingIndent = $value - $this->indent;
            if ($incomingIndent < 0) {
                // indent = indent = current_indent - barang_datang
                $this->addIndent($value * -1);

                // available_stock = current_available_stock + barang_datang
                $this->increment('stock', $value);

                // total_stock = current_available_stock + current_indent
                $this->addTotalStock($this->total_stock * -1); // first, set to 0
                $this->addTotalStock($this->stock + $this->indent); // then, fill total stock
            } else {
                // indent = 0
                $this->addIndent($this->indent * -1);

                // available_stock = current_available_stock + barang_datang
                $this->increment('stock', $incomingIndent);

                // total_stock = current_available_stock + barang_datang
                $this->addTotalStock($this->total_stock * -1); // first, set to 0
                $this->addTotalStock($this->stock); // then, fill total stock
            }
        } else {
            // available_stock = current_available_stock + barang datang
            $this->increment('stock', $value);

            // total_stock = current_total_stock + barang datang
            $this->addTotalStock($value);
        }

        // if ($this->refresh()->stock < 0) {
        //     throw new InsufficientStockException();
        // }
    }

    /**
     * @param int $value
     * @throws InsufficientStockException
     */
    public function addStock(int $value)
    {
        if ($this->stock + $value < 0) {
            throw new InsufficientStockException();
        }

        if ($this->indent == 0) {
            $this->increment('stock', $value);
        } else {
            $newStock = $this->indent - $value;
            if ($newStock < 0) {
                $this->addIndent($this->indent * -1);
                $this->increment('stock', abs($newStock));
            } else {
                $this->addIndent($value * -1);
            }
        }

        // if ($this->refresh()->stock < 0) {
        //     throw new InsufficientStockException();
        // }
    }

    public function addIndent(int $value)
    {
        $this->increment('indent', $value);
    }

    public function addTotalStock(int $value)
    {
        $this->increment('total_stock', $value);
    }

    /**
     * @param int $value
     * @throws InsufficientStockException
     */
    public function deductStock(int $value)
    {
        // $this->addStock($value * -1);
        // $this->debug($value);
        $quantityRequired = $value * -1;
        // $this->debug($quantityRequired);
        $newStock = $this->stock + $quantityRequired;
        // $this->debug($newStock);
        if ($newStock < 0) {
            $this->addStock($this->stock * -1);
            $this->addIndent(abs($newStock));
        } else {
            $this->addStock($quantityRequired);
        }
    }

    public function deductStockWithRefreshTotalStock(int $value)
    {
        $value = $value * -1;
        $this->increment('stock', $value);
        $this->addTotalStock($value);
    }

    public function scopeWhereProductId($query, ...$ids)
    {
        return $query->whereIn('product_id', $ids);
    }

    public function scopeWhereChannelId($query, ...$ids)
    {
        return $query->whereIn('channel_id', $ids);
    }

    public function scopeOutstandingShipment($query, $companyId, $channelId, $productId)
    {
        $total = DB::table('orders')->join('order_details', 'order_details.order_id', '=', 'orders.id')
            ->where('orders.payment_status', OrderPaymentStatus::SETTLEMENT())->where('orders.status', OrderStatus::SHIPMENT())->where('orders.company_id', $companyId)->where('orders.channel_id', $channelId)->where('order_details.product_id', $productId)->select(DB::raw('sum(order_details.quantity) as outstanding_shipment'))->groupBy('orders.id')->get()->sum('outstanding_shipment');
        return $total;
        // return $total->outstanding_shipment ?? 0;
    }

    public function scopeOutstandingShipmentDetail($query, $companyId, $channelId, $productId)
    {
        return Order::with('order_details')->where('orders.payment_status', OrderPaymentStatus::SETTLEMENT())->where('orders.status', OrderStatus::SHIPMENT())->where('company_id', $companyId)->where('channel_id', $channelId)->whereHas('order_details', function ($query) use ($productId) {
            $query->where('product_id', $productId);
        })->get();
    }
}
