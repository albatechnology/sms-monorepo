<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ActivityBrandValue extends Model
{
    protected $guarded = [];

    protected $casts = [
        'activity_id' => 'integer',
        'order_id' => 'integer',
        'user_id' => 'integer',
        'lead_id' => 'integer',
        'product_brand_id' => 'integer',
        'estimated_value' => 'integer',
        'order_value' => 'integer',
        'total_discount' => 'double',
        'total_order_value' => 'double',
    ];

    public function getActiveEstimations(?int $lead_id = null)
    {
        $data = self::whereNull('activity_id')->whereNull('order_id')->get();

        if ($lead_id) $data = $data->where('lead_id', $lead_id);

        return $data->get();
    }

    public function productBrand()
    {
        return $this->belongsTo(ProductBrand::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWhereMy($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function scopeWhereActive($query)
    {
        return $query->whereNull('order_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeWhereCreatedAtRange($query, $startDate = null, $endDate = null)
    {
        if (is_null($startDate)) $startDate = Carbon::now()->startOfMonth();
        if (is_null($endDate)) $endDate = Carbon::now()->endOfMonth();

        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereDate('created_at', '>=', $startDate);
            $q->whereDate('created_at', '<=', $endDate);
        });
    }
}
