<?php


namespace App\Traits;

use App\Exceptions\UnauthorisedTenantAccessException;
use App\Models\Channel;
use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;

/**
 * Trait for model that is company based (i.e., model has company_id)
 *
 * Trait IsCompanyTenanted
 * @package App\Traits
 */
trait IsCompanyTenanted
{
    use IsTenanted;

    public function scopeTenanted($query)
    {
        $hasActiveCompany = tenancy()->getActiveCompany();
        $user             = tenancy()->getUser();

        if (!$hasActiveCompany && !$user->is_admin) throw new Exception('Non admin must have an active company!');
        return $hasActiveCompany ? $query->tenantedActiveCompany($hasActiveCompany) : $query;
    }

    /**
     * Active channel selected. Product is company based, so
     * return scope based on company
     * @param $query
     * @param Channel|null $activeChannel
     * @return mixed
     */
    public function scopeTenantedActiveChannel($query, Channel $activeChannel = null)
    {
        return $this->scopeTenantedActiveCompany($query);
    }

    /**
     * Determine whether currently authenticated user have access to this model
     * @param User|null $user
     * @return bool
     */
    public function userCanAccess(User $user = null): bool
    {
        if (!$user) $user = tenancy()->getUser();
        if ($user->is_admin) return true;
        return $this->company_id == $user->company_id;
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
