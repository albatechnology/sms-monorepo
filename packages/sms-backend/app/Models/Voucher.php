<?php

namespace App\Models;

use App\Interfaces\Tenanted;
use App\Traits\IsCompanyTenanted;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends BaseModel implements Tenanted
{
    use SoftDeletes, IsCompanyTenanted;
    public $table = 'vouchers';

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
