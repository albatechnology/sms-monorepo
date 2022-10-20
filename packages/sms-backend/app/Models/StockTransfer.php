<?php

namespace App\Models;

use App\Enums\StockTransferStatus;
use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperStockTransfer
 */
class StockTransfer extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'stock_transfers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'company_id',
        'from_channel_id',
        'to_channel_id',
        'product_id',
        'stock_from_id',
        'stock_to_id',
        'requested_by_id',
        'approved_by_id',
        'amount',
        'status',
        'cart_id',
        'order_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'cart_id'   => 'integer',
        'order_id'   => 'integer',
        'from_channel_id'   => 'integer',
        'to_channel_id'   => 'integer',
        'product_id'   => 'integer',
        'stock_from_id'   => 'integer',
        'stock_to_id'     => 'integer',
        'requested_by_id' => 'integer',
        'approved_by_id'  => 'integer',
        'amount'          => 'integer',
        'company_id'      => 'integer',
        'status'          => StockTransferStatus::class,
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function fromChannel()
    {
        return $this->belongsTo(Channel::class, 'from_channel_id');
    }

    public function toChannel()
    {
        return $this->belongsTo(Channel::class, 'to_channel_id');
    }

    // public function stock_from()
    // {
    //     return $this->belongsTo(Stock::class, 'stock_from_id');
    // }

    // public function stock_to()
    // {
    //     return $this->belongsTo(Stock::class, 'stock_to_id');
    // }

    // public function requested_by()
    // {
    //     return $this->belongsTo(User::class, 'requested_by_id');
    // }

    // public function approved_by()
    // {
    //     return $this->belongsTo(User::class, 'approved_by_id');
    // }

    // public function item_from()
    // {
    //     return $this->belongsTo(Item::class, 'item_from_id');
    // }

    // public function item_to()
    // {
    //     return $this->belongsTo(Item::class, 'item_to_id');
    // }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class, 'stock_transfer_id', 'id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
