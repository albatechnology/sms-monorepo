<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperItemProductUnit
 */
class ItemProductUnit extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'item_product_units';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'product_unit_id',
        'item_id',
        'uom',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function product_unit()
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
