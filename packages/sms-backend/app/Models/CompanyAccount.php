<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\IsCompanyTenanted;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperCompanyAccount
 */
class CompanyAccount extends BaseModel
{
    use SoftDeletes, Auditable, IsCompanyTenanted;

    public $table = 'company_accounts';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'bank_name',
        'account_name',
        'account_number',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
    ];

}