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
        // 'start_time',
        // 'end_time',
        'is_active' => 'boolean',
        'min_order_price' => 'integer',
        'company_id' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
