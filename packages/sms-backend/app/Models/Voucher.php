<?php

namespace App\Models;

use App\Interfaces\Tenanted;
use App\Traits\IsCompanyTenanted;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends BaseModel implements Tenanted
{
    use SoftDeletes, IsCompanyTenanted;
    public $table = 'vouchers';
    // protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'description',
        'value',
        'start_time',
        'end_time',
        'is_active',
        'min_order_price',
        'company_id',
    ];

    protected $casts = [
        'id' => 'string',
        'value' => 'integer',
        'start_time' => 'date',
        'end_time' => 'date',
        'is_active' => 'boolean',
        'min_order_price' => 'integer',
        'company_id' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function customerVouchers()
    {
        return $this->hasMany(CustomerVoucher::class, 'voucher_id');
    }

    public function scopeWhereIsActive($query, $value = true)
    {
        return $query->where('is_active', $value);
    }

    public function scopeWhereCodeLike($query, $key)
    {
        return $query->orWhere('id', 'LIKE', "%" . $key . "%");
    }

    public function scopeWhereStartTimeAfter($query, $datetime)
    {
        return $query->whereDate('start_time', '>=', date('Y-m-d', strtotime($datetime)));
    }

    public function scopeWhereEndTimeBefore($query, $datetime)
    {
        return $query->whereDate('end_time', '<=', date('Y-m-d', strtotime($datetime)));
    }

    public function isActiveNow(): bool
    {
        if (!$this->is_active) return false;
        if (!is_null($this->start_time) && !is_null($this->end_time)) {
            if (now()->isBefore($this->start_time)) return false;
            if (!empty($this->end_time) && now()->isAfter($this->end_time)) return false;
        }

        return true;
    }

    public function toRecord()
    {
        $data = $this->toArray();
        unset($data['created_at'], $data['updated_at'], $data['deleted_at']);

        return $data;
    }
}
