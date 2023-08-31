<?php


namespace App\Traits;

use App\Exceptions\UnauthorisedTenantAccessException;
use App\Models\Channel;
use App\Models\Company;
use App\Models\User;
use App\Services\CacheService;
use Exception;
use Illuminate\Support\Collection;

trait IsTenanted
{
    public function scopeTenanted($query)
    {
        $hasActiveChannel = tenancy()->getActiveTenant();
        // $hasActiveCompany = tenancy()->getActiveCompany();

        if ($hasActiveChannel) return $query->tenantedActiveChannel($hasActiveChannel);

        $user = tenancy()->getUser();

        if ($user->is_super_admin) return $query;

        return $query->tenantedUserChannels();

        // // if (!$hasActiveCompany && !$user->is_admin) throw new Exception('Non admin must have an active company!');

        // if (!$hasActiveCompany) return $query;

        // $query = $query->tenantedActiveCompany($hasActiveCompany);

        // return $user->is_admin ? $query : $query->tenantedUserChannels();
    }

    public function scopeFindTenanted($query, int $id)
    {
        return $query->tenanted()->where('id', $id)->firstOrFail();
    }

    /**
     * Active channel selected.
     * Show only resource that belong to the selected channel
     * @param $query
     * @param Channel|null $activeChannel
     * @return mixed
     */
    public function scopeTenantedActiveChannel($query, Channel $activeChannel = null)
    {
        if (!$activeChannel) $activeChannel = tenancy()->getActiveTenant();
        return $query->where('channel_id', $activeChannel->id);
    }

    /**
     * Active Company selected.
     * Show only resource that belong to the selected company.
     * This may need to be modified per model basis
     * @param $query
     * @param Company|null $activeCompany
     * @return mixed
     */
    public function scopeTenantedActiveCompany($query, Company $activeCompany = null)
    {
        if (!$activeCompany) $activeCompany = tenancy()->getActiveCompany();

        // There is several possibility on how to relate company from the model
        // 1. company_id is available on the model
        // 2. Company relationship from channel.company
        // 3. Company relationship from channels.company (exceptional cases such as User model)

        if (method_exists(static::class, 'company')) {
            return $query->where('company_id', $activeCompany->id);
        } elseif (method_exists(static::class, 'channel')) {
            return $query->whereHas('channel', fn ($query) => $query->where('company_id', $activeCompany->id));
        } else {
            return $query->whereHas('channels', function ($query) use ($activeCompany) {
                $query->whereIn('id', tenancy()->getTenants()->pluck('id'))
                    ->whereIn('company_id', $activeCompany->id);
            });
        }
    }

    /**
     * Show only resource that belong to a set of channels that user have access to.
     *
     * @param $query
     * @param Collection|null $tenants
     * @return mixed
     * @throws Exception
     */
    public function scopeTenantedUserChannels($query, Collection $tenants = null)
    {
        if (!$tenants) $tenants = tenancy()->getTenants();
        if (method_exists(static::class, 'channel')) {
            return $query->whereIn('channel_id', $tenants->pluck('id'));
        } elseif (method_exists(static::class, 'channels')) {
            return $query->whereHas('channels', function ($query) use ($tenants) {
                $query->whereIn('id', $tenants->pluck('id'));
            });
        } else {
            throw new Exception('Unknown relationship to channels from model ' . static::class);
        }
    }

    public function getCompanyIdAttribute($value)
    {
        if (!is_null($value)) {
            return $value;
        }

        return app(CacheService::class)->companyOfChannel($this->channel_id)->id;
    }

    /**
     * @param User|null $user
     * @return IsTenanted
     * @throws UnauthorisedTenantAccessException
     * @throws Exception
     */
    public function checkTenantAccess(User $user = null): static
    {
        if (!$this->userCanAccess($user)) {
            throw new UnauthorisedTenantAccessException();
        }
        return $this;
    }

    /**
     * Determine whether currently authenticated user have access to this model
     * Default setting is user have access if the resource is allocated to a channel
     * that the user have access to
     * @param User|null $user
     * @return bool
     * @throws Exception
     */
    public function userCanAccess(User $user = null): bool
    {
        if (!$user) $user = tenancy()->getUser();
        if ($user->is_admin) return true;

        $tenants = tenancy()->getTenants();

        if (method_exists(static::class, 'channel')) {
            return $tenants->pluck('id')->contains($this->channel_id);
        } elseif (method_exists(static::class, 'channels')) {
            return $this->channels->pluck('id')->contains(function ($id) use ($tenants) {
                return $tenants->pluck('id')->contains($id);
            });
        } else {
            throw new Exception('Unknown relationship to channel from model ' . static::class);
        }
    }

    public function tenant()
    {
        return $this->belongsTo(Channel::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}
