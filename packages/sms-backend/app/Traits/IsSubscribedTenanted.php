<?php


namespace App\Traits;

// use App\Exceptions\UnauthorisedTenantAccessException;
use App\Models\Channel;
// use App\Models\Company;
use App\Models\SubscribtionUser;
use App\Models\User;

/**
 * Trait for model that is company based (i.e., model has company_id)
 *
 * Trait IsCompanyTenanted
 * @package App\Traits
 */
trait IsSubscribedTenanted
{
    use IsTenanted;

    public function scopeTenanted($query)
    {
        $user = tenancy()->getUser();
        if ($user->is_super_admin) return $query;
        return $query->where('subscribtion_user_id', $user->subscribtion_user_id);
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
        return $this->subscribtion_user_id == $user->subscribtion_user_id;
        // return $this->company_id == $user->company_id;
    }

    public function subscribtionUser()
    {
        return $this->belongsTo(SubscribtionUser::class, 'subscribtion_user_id');
    }
}
