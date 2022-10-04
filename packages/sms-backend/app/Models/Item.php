<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperItem
 */
class Item extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'items';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'code',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function itemItemProductUnits()
    {
        return $this->hasMany(ItemProductUnit::class, 'item_id', 'id');
    }

    public function itemStocks()
    {
        return $this->hasMany(Stock::class, 'item_id', 'id');
    }

    public function itemFromStockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'item_from_id', 'id');
    }

    public function itemToStockTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'item_to_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
