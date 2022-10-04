<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperOrderTracking
 */
class OrderTracking extends BaseModel
{
    use SoftDeletes;

    public $table = 'order_trackings';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const TYPE_SELECT = [
        'status_update' => 'Status Update',
        'detail_update' => 'Detail Update',
    ];

    protected $fillable = [
        'order_id',
        'type',
        'context',
        'old_value',
        'new_value',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
