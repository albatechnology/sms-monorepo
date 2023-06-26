<?php

namespace App\Services;

use App\Enums\UserType;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Models\Channel;
use App\Models\ChannelUser;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Validator;

/**
 * In this application tenancy is realised in the form of company model.
 * So when we use tenant as a concept, the implementation would actually
 * be the company model.
 *
 * Class ProductCriteriaService.
 */
class MultiTenancyService
{
    /**
     * MultiTenancyService constructor.
     * @param User|null $user impersonate as an user
     */
    public function __construct(protected ?User $user = null)
    {
    }

    /**
     * Get active tenant to use for the logged in user
     * @return Channel|null
     */
    function getActiveTenant(): ?Channel
    {
        return session('active-tenant');
    }

    /**
     * Get active company to use for the logged in user.
     * For non admin or director, the active company is stored in DB against user as company_id.
     * For admin and director, it may be null (means select all company) or a company saved on the session
     * @return Company|null
     */
    // function getActiveCompany(): ?Company
    // {
    //     $user = $this->getUser();
    //     if (!$user) abort(500, "Can't get active company, no login session detected.");

    //     if ($user->type->is(UserType::DIRECTOR) || $user->is_admin) {
    //         return session('active-company');

    //         //            if(session('active-company')) return session('active-company');
    //         //
    //         //            if($channel = $this->getActiveTenant()){
    //         //                return $channel->company;
    //         //            }
    //     }

    //     return $user->company;
    // }

    /**
     * Set active company to use for the logged in user
     * @param Company $company
     * @return Company|null
     */
    // function setActiveCompany(Company $company): ?Company
    // {
    //     $user = $this->getUser();

    //     if (!$user) abort(500, "Can't set active company, no login session detected.");

    //     if (!($user->type->is(UserType::DIRECTOR) || $user->is_admin)) {
    //         return null;
    //     }

    //     return session(['active-company' => $company]);
    // }

    /**
     * Set active tenant to use for the logged in user
     * @param Channel $channel
     * @return Channel|null
     * @throws UnauthorisedTenantAccessException
     */
    function setActiveTenant(Channel $channel): ?Channel
    {
        $user = $this->getUser();

        if (!$user) abort(500, "Can't set active channel, no login session detected.");

        $tenants = $this->getTenants();
        $allowedTenantIds = $tenants->isEmpty() ? collect([]) : $tenants->pluck('id');

        // not allowed
        if (!$allowedTenantIds->contains($channel->id)) {
            throw new UnauthorisedTenantAccessException();
        }

        return session(['active-tenant' => $channel]);
    }

    /**
     * Get all tenants assigned to logged in user
     * @param  User|null  $user
     * @return mixed|null
     */
    public function getTenants(User $user = null): ?Collection
    {
        $user = $this->getUser($user);

        // no user, return null, or perhaps even exception?
        if (!$user) return null;

        if ($user->is_super_admin) {
            return Channel::get();
        }

        if ($user->is_admin || $user->is_director) {
            // return Channel::get();
            return Channel::where('subscribtion_user_id', $user->subscribtion_user_id)->get();
        }

        return $user->channels;
    }

    /**
     * Get all companies accessible to the logged in user
     * @return Collection|null
     */
    // public function getCompanies(): ?Collection
    // {
    //     $user = $this->getUser();
    //     if (!$user) return null;

    //     if ($user->type->is(UserType::DIRECTOR) || $user->is_admin) {
    //         return Company::all();
    //     } else {
    //         return collect([$user->company]);
    //     }
    // }

    /**
     * Set the given tenant as current active tenant
     * @param Request $request
     * @throws ValidationException
     */
    public function setActiveTenantFromRequest(Request $request)
    {
        $user = $this->getUser();

        if (!$user) abort(500, "Can't set tenant, no login session detected.");


        $validator = Validator::make($request->all(), [
            'channel_id' => 'nullable|exists:channels,id',
            // 'company_id' => 'nullable|exists:companies,id'
        ]);

        if ($validator->fails()) {
            session()->forget('active-tenant');
            // session()->forget('active-company');
        } else {
            $validated = $validator->validated();

            $validated['channel_id'] ? $this->setActiveTenant(Channel::findOrfail($validated['channel_id'])) : session()->forget('active-tenant');

            // if ($validated['company_id']) {
            //     $company = Company::find($validated['company_id']);
            //     if (!$this->activeCompanyIs($company)) {
            //         session()->forget('active-tenant');
            //         $this->setActiveCompany($company);
            //     }
            // } else {
            //     session()->forget('active-company');
            // }
        }
    }

    /**
     * Check whether the currently active tenant is the same
     * as the given tenant parameter
     * @param  Channel  $channel
     * @return bool
     */
    public function activeTenantIs(Channel $channel): bool
    {
        if (!$activeTenant = $this->getActiveTenant()) return false;
        return $activeTenant->id === $channel->id;
    }

    /**
     * Check whether the currently active tenant is the same
     * as the given tenant parameter
     * @param  Company|int $company
     * @return bool
     */
    // public function activeCompanyIs(Company|int $company): bool
    // {
    //     if (!$activeCompany = $this->getActiveCompany()) return false;

    //     if (is_int($company)) {
    //         $company = Company::findOrFail($company);
    //     }

    //     return $activeCompany->id === $company->id;
    // }

    /**
     * Check whether current logged in user have access to a given tenant
     *
     * @param  int  $channel_id
     * @return bool
     */
    public function hasTenantId(int $channel_id): bool
    {
        return ChannelUser::where('user_id', $this->getUser()->id)->where('channel_id', $channel_id)->get()->isNotEmpty();
    }

    public function getUser(): ?User
    {
        $user = $this->user ?? auth('sanctum')->user();
        $user = $user ?? auth()->user();
        return $user;
    }
}
