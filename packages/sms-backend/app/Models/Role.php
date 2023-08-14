<?php

namespace App\Models;

// use App\Interfaces\TenantedInterface;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends ModelsRole
{
    public $table = 'roles';
    protected $guarded = [];

    protected static function booted()
    {
        static::retrieved(function ($model) {
        });
    }

    public function scopeTenanted($query)
    {
        $hasActiveTenant = tenancy()->getActiveTenant();
        if ($hasActiveTenant) $query->where('subscribtion_user_id', $hasActiveTenant->subscribtionUser->id);

        // $hasActiveCompany = tenancy()->getActiveCompany();
        // if ($hasActiveCompany) $query->where('subscribtion_user_id', $hasActiveCompany->id);

        $user = user();
        if ($user->is_super_admin) return $query;

        return $query->wherePublicRole()->orWhere('subscribtion_user_id', $user->subscribtion_user_id);

        // return $query->wherePublicRole()->whereIn('subscribtion_user_id', tenancy()->getMyAllCompanies()?->pluck('id') ?? []);
    }

    public function scopeFindTenanted($query, int $id)
    {
        return $query->tenanted()->where('id', $id)->firstOrFail();
    }

    public function scopeWherePublicRole($query)
    {
        return $query->where('subscribtion_user_id', '!=', 1)->orWhereNull('subscribtion_user_id');
    }

    public function subscribtionUser()
    {
        return $this->belongsTo(SubscribtionUser::class);
    }

    // public function scopeTenanted($query)
    // {
    //     $hasActiveTenant = tenancy()->getActiveTenant();
    //     if ($hasActiveTenant) $query->where('company_id', $hasActiveTenant->company->id);

    //     // $hasActiveCompany = tenancy()->getActiveCompany();
    //     // if ($hasActiveCompany) $query->where('company_id', $hasActiveCompany->id);

    //     $user = user();
    //     if ($user->is_super_admin) return $query;

    //     // return $query->wherePublicRole()->whereIn('company_id', tenancy()->getMyAllCompanies()?->pluck('id') ?? []);
    // }

    // public function scopeFindTenanted($query, int $id)
    // {
    //     return $query->tenanted()->where('id', $id)->firstOrFail();
    // }

    // // public function company()
    // // {
    // //     return $this->belongsTo(Company::class, 'company_id');
    // // }

    // public function scopeWherePublicRole($query)
    // {
    //     return $query->where('company_id', '!=', 1);
    // }
}
