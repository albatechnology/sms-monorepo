<?php

namespace App\Models;

class CustomerVoucher extends BaseModel
{
    protected $casts = [
        'customer_id' => 'integer',
        'is_used' => 'boolean',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'id');
    }

    public function scopeWhereIsActive($query, $value = true)
    {
        return $query->whereHas('voucher', fn ($q) => $q->whereIsActive($value));
    }

    public function scopeWhereCodeLike($query, $key)
    {
        return $query->whereHas('voucher', fn ($q) => $q->whereCodeLike($key));
    }

    public function scopeWhereStartTimeAfter($query, $datetime)
    {
        return $query->whereHas('voucher', fn ($q) => $q->whereStartTimeAfter($datetime));
    }

    public function scopeWhereEndTimeBefore($query, $datetime)
    {
        return $query->whereHas('voucher', fn ($q) => $q->whereEndTimeBefore($datetime));
    }
}
