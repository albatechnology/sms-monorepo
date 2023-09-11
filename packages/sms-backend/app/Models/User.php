<?php

namespace App\Models;

use App\Classes\CartItem;
use App\Enums\UserType;
use App\Interfaces\ReportableScope;
use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\CustomCastEnums;
use App\Traits\IsTenanted;
use App\Traits\SaveToSubscriber;
use BenSampo\Enum\Traits\CastsEnums;
use Carbon\Carbon;
use DateTimeInterface;
use Exception;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements Tenanted, ReportableScope
{
    use HasRoles;
    use HasApiTokens, SoftDeletes, Notifiable, Auditable, HasFactory, NodeTrait, IsTenanted, CastsEnums, CustomCastEnums, SaveToSubscriber;

    public $table = 'users';

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'subscribtion_user_id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'type',
        'supervisor_type_id',
        'supervisor_id',
        'company_id',
        'channel_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'company_ids',
    ];

    protected $casts = [
        'company_id'    => 'integer',
        'supervisor_id' => 'integer',
        'supervisor_type_id' => 'integer',
        'channel_id'    => 'integer',
        'type'          => UserType::class,
        'company_ids'   => 'array',
    ];

    public static function boot()
    {
        self::deleted(function (self $model) {
        });

        parent::boot();
    }

    /**
     * required setup for nested tree package
     * @return string
     */
    public function getParentIdName(): string
    {
        return 'supervisor_id';
    }

    /**
     * Only applies to supervisor
     */
    public function getAllChildrenSales()
    {
        if (!$this->is_supervisor) {
            throw new Exception(sprintf(
                'getAllChildrenSales() can only be called from a supervisor! Called by user id %s',
                $this->id
            ));
        }

        return User::whereDescendantOf($this->id)->whereIsSales()->get();
    }

    /**
     * Only applies to supervisor
     */
    public function getAllChildrenSupervisors($supervisor_type_id = null)
    {
        if (!$this->is_supervisor) {
            throw new Exception(sprintf(
                'getAllChildrenSupervisors() can only be called from a supervisor! Called by user id %s',
                $this->id
            ));
        }

        $users = User::whereDescendantOf($this->id)->whereIsSupervisor();
        if ($supervisor_type_id != null) {
            $users = $users->where('supervisor_type_id', $supervisor_type_id);
        }
        return $users->get();
    }

    /**
     * Only applies to supervisor
     * @throws Exception
     */
    public function getDescendantIds(): Collection
    {
        if (!$this->is_supervisor) {
            throw new Exception(sprintf(
                'getDescendants() can only be called from a supervisor! Called by user id %s',
                $this->id
            ));
        }

        return User::whereDescendantOf($this->id)->get(['id'])->pluck('id');
    }

    public function getSalesFriends()
    {
        if (!$this->is_sales) {
            throw new Exception(sprintf(
                'getSalesFriends() can only be called from a sales! Called by user id %s',
                $this->id
            ));
        }

        return User::where('supervisor_id', $this->supervisor_id)->whereIsSales()->get();
    }

    public function getSalesSupervisor($supervisorType = 2)
    {
        if (!$this->is_sales) {
            throw new Exception(sprintf(
                'getSalesSupervisor() can only be called from a sales! Called by user id %s',
                $this->id
            ));
        }

        $storeLeader = User::where('id', $this->supervisor_id)->first();
        if (!$storeLeader) throw new Exception(sprintf('getSalesSupervisor() can only be called from a sales! Called by user id %s', $this->id));
        if ($supervisorType == 1) return $storeLeader;

        // return BUM
        $bum = User::where('id', $storeLeader->supervisor_id)->first();
        if (!$bum) return $storeLeader;
        return $bum;
    }

    //region Scopes

    public function scopeCustomDescendantOf($query)
    {
        $user = auth()->user();
        $user_type = auth()->user()->type;
        if ($user_type->is(UserType::DIRECTOR())) {
            return $query->where('company_id', $user->company_id)->whereIn('type', [UserType::SUPERVISOR, UserType::SALES]);
        } elseif ($user_type->is(UserType::SUPERVISOR())) {
            if ($user->supervisor_type_id == 1) {
                // Store Leader
                return $query->where('supervisor_id', $user->id);
            }
            return $query->whereDescendantOf($user->id);
        }
        // } elseif ($user_type->is(UserType::SUPERVISOR()) && $user->supervisor_type_id == 2) {
        //     // BUM / manager area
        //     return $query->where('company_id', $user->company_id)->where(function ($query) use ($user) {
        //         $query->where('supervisor_id', $user->id)->orWhere('type', UserType::SALES);
        //     });
        // } elseif ($user_type->is(UserType::SUPERVISOR()) && $user->supervisor_type_id == 1) {
        //     // Store Leader
        //     return $query->where('company_id', $user->company_id)->where('supervisor_id', $user->id);
        // }
        // show all supervisor and sales by company_id
        return $query->where('company_id', $user->company_id)->whereIn('type', [UserType::SUPERVISOR, UserType::SALES]);
    }

    public function scopeWhereChannelId($query, $id)
    {
        return $query->where('channel_id', $id);
    }

    public function scopeWhereSupervisorId($query, ...$ids)
    {
        return $query->whereIn('supervisor_id', $ids);
    }

    public function scopeWhereSupervisorTypeId($query, ...$ids)
    {
        return $query->whereIn('supervisor_type_id', $ids);
    }

    public function scopeWhereIsSupervisor($query)
    {
        return $query->where('type', UserType::SUPERVISOR);
    }

    public function scopeWhereIsSales($query)
    {
        return $query->where('type', UserType::SALES);
    }

    public function scopeWhereCompanyId($query, int $id)
    {
        return $query->where('company_id', $id);
    }

    public function scopeWhereCompanyIds($query, array $ids = [])
    {
        return $query->whereIn('company_id', $ids);
    }

    /**
     * Override tenanted scope.
     *
     * @param $query
     * @return mixed
     */
    public function scopeTenanted($query): mixed
    {
        $hasActiveChannel = tenancy()->getActiveTenant();
        // $hasActiveCompany = tenancy()->getActiveCompany();

        if ($hasActiveChannel) {
            return $query->whereHas('channels', function ($query) {
                $query->where('id', tenancy()->getActiveTenant()->id);
            });
        }

        $user = tenancy()->getUser();
        $isSuperAdmin = $user->is_super_admin;

        if ($isSuperAdmin) {
            return $query;
        }

        return $query->where('subscribtion_user_id', $user->subscribtion_user_id);

        // if ($hasActiveCompany) {
        //     if ($isAdmin) {
        //         // lets admin see all channels in a company
        //         return $query->whereHas('channels', function ($query) use ($hasActiveCompany) {
        //             $query->whereIn('company_id', $hasActiveCompany->id);
        //         });
        //     } else {
        //         // lets user see all resource available to the user's channel within a company
        //         return $query->whereHas('channels', function ($query) use ($hasActiveCompany) {
        //             $query->whereIn('id', tenancy()->getTenants()->pluck('id'))
        //                 ->whereIn('company_id', $hasActiveCompany->id);
        //         });
        //     }
        // } else {
        // $isAdmin = $user->is_admin;
        // if ($isAdmin) {
        //     // lets admin see all
        //     return $query;
        // } else {
        //     // lets user see all resource available to the user's channel
        //     return $query->whereHas('channels', function ($query) {
        //         $query->whereIn('id', tenancy()->getTenants()->pluck('id'));
        //     });
        // }
        // }
    }


    //endregion

    //region Attributes

    public function setSupervisorIdAttribute($value)
    {
        $this->setParentIdAttribute($value);
    }

    public function getIsSalesAttribute(): bool
    {
        return $this->type->is(UserType::SALES);
    }

    public function getIsSupervisorAttribute(): bool
    {
        return $this->type->is(UserType::SUPERVISOR);
    }

    public function getIsSuperAdminAttribute(): bool
    {
        if ($this->is_director) return true;
        // return $this->roles()->where('id', 1)->exists();
        return $this->hasRole('super-admin');
    }

    public function getIsAdminAttribute(): bool
    {
        if ($this->is_director) return true;
        // return $this->roles()->where('id', 1)->exists();
        return $this->hasRole('admin');
    }

    // public function roles(): BelongsToMany
    // {
    //     return $this->belongsToMany(Role::class);
    // }

    // public function hasRole(string $role)
    // {
    //     if (in_array($role, $this->roles->pluck('title')->toArray())) {
    //         return true;
    //     }
    //     return false;
    // }

    // public function permissions()
    // {
    //     return $this->hasMany(PermissionUser::class);
    // }

    public function userCompanies()
    {
        return $this->hasMany(UserCompany::class);
    }

    public function getIsDevAttribute(): bool
    {
        return $this->is_admin;
    }

    public function getIsDirectorAttribute(): bool
    {
        return $this->type->is(UserType::DIRECTOR);
    }

    // public function getIsDIGITAL_MARKETINGAttribute(): bool
    // {
    //     return $this->type->is(UserType::DIGITAL_MARKETING);
    // }

    public function getEmailVerifiedAtAttribute($value): ?string
    {
        if (!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::parse($value) : null;
    }

    //endregion

    // region Relationships

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function cartDemand()
    {
        return $this->hasOne(CartDemand::class);
    }

    public function supervisorType(): BelongsTo
    {
        return $this->belongsTo(SupervisorType::class, 'supervisor_type_id');
    }

    public function supervisor_type(): BelongsTo
    {
        return $this->belongsTo(SupervisorType::class, 'supervisor_type_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * @return BelongsToMany
     * @deprecated this relationship and table should not be used.
     *    User should only use belongsTo to company
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function notificationDevices(): HasMany
    {
        return $this->hasMany(NotificationDevice::class);
    }

    public function latestUserActivities()
    {
        return $this->hasOne(Activity::class, 'user_id', 'id')->groupBy('lead_id');
    }

    public function userActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'user_id', 'id');
    }

    public function grouped_userActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'user_id', 'id')->groupBy('lead_id');
    }

    public function grouped_userLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'user_id', 'id');
    }

    public function activityBrandValues(): HasMany
    {
        return $this->hasMany(ActivityBrandValue::class, 'user_id', 'id');
    }

    public function userActivityComments(): HasMany
    {
        return $this->hasMany(ActivityComment::class, 'user_id', 'id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'user_id', 'id');
    }

    public function userOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function approvedByPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'approved_by_id', 'id');
    }

    public function fulfilledByShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'fulfilled_by_id', 'id');
    }

    public function fulfilledByInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'fulfilled_by_id', 'id');
    }

    public function supervisorUsers(): HasMany
    {
        return $this->hasMany(User::class, 'supervisor_id', 'id');
    }

    public function requestedByStockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'requested_by_id', 'id');
    }

    public function approvedByStockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'approved_by_id', 'id');
    }

    public function channelsPivot(): HasMany
    {
        return $this->hasMany(ChannelUser::class);
    }

    public function qaTopics(): HasMany
    {
        return $this->hasMany(QaTopic::class, 'creator_id', 'id');
    }

    /**
     * add whereHas condition where subscribtion_user_id = user.subscribtion_user_id
     */
    public function supervisorApprovalLimits(): HasMany
    {
        return $this->hasMany(SupervisorDiscountApprovalLimit::class, 'supervisor_type_id', 'supervisor_type_id');
    }

    public function userUserAlerts(): BelongsToMany
    {
        return $this->belongsToMany(UserAlert::class);
    }

    public function productBrands(): BelongsToMany
    {
        return $this->belongsToMany(ProductBrand::class, 'product_brand_users');
    }

    /**
     * Default channel selected
     * @return BelongsTo
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class);
    }

    // endregion

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function syncCart(CartItem $cartItem): Cart
    {
        $cart = Cart::updateOrCreate(
            ['user_id' => $this->id],
            [
                'items' => $cartItem,
            ]
        );

        $cart->updatePricesFromItemLine();
        $cart->save();

        return $cart;
    }

    public function syncCartDemand(CartItem $cartItem): CartDemand
    {
        $cart = CartDemand::updateOrCreate(
            ['user_id' => $this->id],
            [
                'items' => $cartItem,
            ]
        );

        // $cart->updatePricesFromItemLine();
        $cart->save();

        return $cart;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * If this user does not have a default channel_id, auto set
     * it only if this user only have access to one channel.
     *
     * @return bool return true if this user now have a default company_id
     */
    public function setDefaultChannel(): bool
    {
        if ($this->channel_id) return true;

        $channels = $this->channels;
        if ($channels->count() == 1) {
            $this->update(['channel_id' => $channels->first()->id]);
            return true;
        }

        return false;
    }

    public function getDefaultChannel()
    {
        if ($this->channel_id) return $this->channel_id;

        $channels = $this->channels;
        if ($channels->count() > 0) {
            return $channels->first()->id;
        }

        return 1;
    }

    /**
     * Create a new personal access token for the user.
     *
     * @param string $name
     * @param array $abilities
     * @return NewAccessToken
     */
    public function createToken(string $name, array $abilities = ['*'])
    {
        $token = $this->tokens()->create([
            'name'             => $name,
            'token'            => hash('sha256', $plainTextToken = Str::random(40)),
            'plain_text_token' => $plainTextToken,
            'abilities'        => $abilities,
        ]);

        return new NewAccessToken($token, $token->id . '|' . $plainTextToken);
    }

    public function getReportLabel(): string
    {
        return $this->name;
    }

    public function scopeWhereSupervisorTypeLevel($query, int $id)
    {
        if ($id === -1) {
            return $query->whereIsSales();
        }

        $ids = cache_service()
            ->supervisorTypes()
            ->filter(fn ($q) => $q->level == $id)
            ->pluck('id')
            ->all();

        return $query->whereIsSupervisor()->whereIn('supervisor_type_id', $ids);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getCompanyIdAttribute($value)
    {
        return (int) $value;
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function targets()
    {
        return $this->morphMany(Target::class, 'model');
    }

    // public function newTargets()
    // {
    //     return $this->morphMany(NewTarget::class, 'model');
    // }

    public function userSalesIds($user, $conditions)
    {
        $users = User::where('type', UserType::SALES());
        $channel_id = $conditions['channel_id'];
        $company_id = $conditions['company_id'];
        if (isset($channel_id) && $channel_id != '') $users = $users->where('channel_id', $channel_id);
        if (isset($company_id) && $company_id != '') $users = $users->where('company_id', $company_id);

        switch ($user->type) {
            case UserType::DIRECTOR():
                return $users->pluck('id');
                break;
            case UserType::SUPERVISOR():
                return $users->where('supervisor_id', $user->id)->pluck('id');
                break;
            case UserType::SALES():
                return [$user->id];
                break;
            default:
                return $users->pluck('id');
                break;
        }
    }

    public function getLimitApproval(): int
    {
        $limit = $this->supervisorType ? ($this->supervisorApprovalLimits()->where('subscribtion_user_id', $this->subscribtion_user_id)->first()?->limit ?? 0) : 0;
        return $limit;
    }

    public function checkLimitApproval(int $total_price): int
    {
        // $limit = $this->supervisorType ? (int) $this->supervisorType->discount_approval_limit_percentage : 0;
        $limit = $this->supervisorType ? ($this->supervisorApprovalLimits()->where('subscribtion_user_id', $this->subscribtion_user_id)->first()?->limit ?? 0) : 0;
        return ((int)$limit / 100) * $total_price;
    }

    public function getMyBrandIds(): array
    {
        if ($this->type->is(UserType::SALES)) {
            return $this->productBrands->pluck('id')->all();
        } elseif (UserType::SUPERVISOR) {
            return ProductBrand::where('company_id', $this->company_id)->pluck('id')->all();
        }

        return ProductBrand::pluck('id')?->all() ?? [];
    }

    public function subscribtionUser()
    {
        return $this->belongsTo(SubscribtionUser::class);
    }

    // public function getActivityTargetAttribute()
    // {
    //     return $this->attributes['activity_target'] = 30;
    // }
}
