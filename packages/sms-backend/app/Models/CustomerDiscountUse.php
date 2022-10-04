<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCustomerDiscountUse
 */
class CustomerDiscountUse extends Model
{
    protected $fillable = [
        'customer_id',
        'discount_id',
        'use_count',
        'order_ids',
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

}