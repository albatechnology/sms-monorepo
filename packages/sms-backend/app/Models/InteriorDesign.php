<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\IsCompanyTenanted;

class InteriorDesign extends BaseModel
{
    use SoftDeletes, IsCompanyTenanted;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $guarded = [];

    protected $casts = [
        'bum_id'        => 'integer',
        'sales_id'      => 'integer',
        'religion_id'   => 'integer',
    ];

    public function bum()
    {
        return $this->belongsTo(User::class, 'bum_id');
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }

    public function scopeCustomTenanted($query)
    {
        $user = tenancy()->getUser();
        if ($user->is_director) {
            $company_ids = $user->company_ids ?? $user->userCompanies->pluck('company_id')->all() ?? [];
            return $query->whereHas('sales', function ($q) use ($company_ids) {
                $q->whereIn('company_id', $company_ids);
            });
        } elseif ($user->is_supervisor) {
            if ($user->supervisor_type_id == 2) {
                return $query->where('bum_id', $user->id);
            } else {
                return $query->whereIn('sales_id', $user->getAllChildrenSales()->pluck('id')->all());
            }
        }

        return $query->where('sales_id', $user->id);
    }
}
