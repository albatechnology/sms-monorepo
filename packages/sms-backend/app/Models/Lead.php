<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\UserType;
use App\Interfaces\Tenanted;
use App\Jobs\LeadStatusChange;
use App\Services\CacheService;
use App\Services\CoreService;
use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsTenanted;
use Carbon\Carbon;
use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperLead
 */
class Lead extends BaseModel implements Tenanted, HasMedia
{
    use SoftDeletes, Auditable, CustomInteractsWithMedia;

    use IsTenanted {
        IsTenanted::scopeTenanted as protected defaultScopeTenanted;
    }

    public $table = 'leads';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'type',
        'status',
        'label',
        'is_new_customer',
        'is_unhandled',
        'group_id',
        'user_id',
        'user_referral_id',
        'customer_id',
        'channel_id',
        'status_history',
        'status_change_due_at',
        'has_pending_status_change',
        'lead_category_id',
        'sub_lead_category_id',
        'interest',
        'created_at',
        'updated_at',
        'deleted_at',
        // 'product_brand_id',
        'parent_id',
        'last_activity_status',
    ];

    protected $casts = [
        'is_new_customer'  => 'bool',
        'is_unhandled'     => 'bool',
        'has_activity'     => 'bool',
        'group_id'         => 'integer',
        'user_id'          => 'integer',
        'user_referral_id' => 'integer',
        'customer_id'      => 'integer',
        'channel_id'       => 'integer',
        'lead_category_id' => 'integer',
        'sub_lead_category_id' => 'integer',
        'status_history'   => 'array',
        'type'             => LeadType::class,
        'status'           => LeadStatus::class,
        'last_activity_status' => \App\Enums\ActivityStatus::class,
        // 'product_brand_id' => 'integer',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::created(function (self $model) {
            if (empty($model->group_id)) {
                $model->update(["group_id" => $model->determineGroupId()]);
            }
        });

        self::saving(function (self $model) {

            // auto generate label when not given
            if (empty($model->label)) {
                $time         = $model->created_at ?? now();
                $name         = $model->customer->full_name;
                $model->label = sprintf("%s - %s", $time->format('Y-m-d'), $name);
            }

            // when a lead is closed as a sale, close other lead in the group
            // if (
            //     $model->getOriginal('type') !== $model->type
            //     && $model->type->is(LeadType::DEAL)
            //     && $model->status->is(LeadStatus::SALES)
            // ) {
            //     // do a mass update to avoid recursive update
            //     Lead::query()
            //         ->where('id', '<>', $model->id)
            //         ->where('group_id', $model->group_id)
            //         ->update(
            //             [
            //                 'type'   => LeadType::DEAL,
            //                 'status' => LeadStatus::OTHER_SALES
            //             ]
            //         );
            // }

            if ($model->isDirty('status') || $model->isDirty('type')) {
                $model->addStatusHistory();
            }
        });


        parent::boot();
    }

    /**
     * Determine the appropriate group id for the model.
     * This is done by taking the previous lead for this lead customer and:
     * 1. Use current id as group id if previous lead group has closed sales lead, otherwise:
     * 2. Use the previous lead group id as this lead's group id
     */
    public function determineGroupId(): int
    {
        // since all leads in the same group is closed together,
        // it is fine to just grab first lead in the latest lead group
        $latestCustomerLeadGroup = Lead::query()
            ->where('customer_id', $this->customer_id)
            ->where('id', '<>', $this->id)
            ->orderBy('group_id', 'desc')
            ->get();

        // first time lead, use new group id
        if ($latestCustomerLeadGroup->isEmpty()) {
            return $this->id;
        }

        // If last lead of this customer is closed, use new order id
        $hasClosedSale = $latestCustomerLeadGroup->contains(function (Lead $lead) {
            return $lead->type->is(LeadType::DEAL);
        });

        // otherwise, combine to the existing group id
        return $hasClosedSale ? $this->id : $latestCustomerLeadGroup->first()->group_id;
    }

    public function addStatusHistory(): array
    {
        return $this->status_history = [...$this->status_history ?? [], [
            'status'     => $this->status->value,
            'type'       => $this->type->value,
            'updated_at' => now()
        ]];
    }

    /**
     * Override default tenanted trait.
     * Sales is scoped to its own leads.
     * Supervisor is scope to the leads of its children sales.
     *
     * @param $query
     * @return mixed
     * @throws Exception
     */
    public function scopeTenanted($query)
    {
        $user = tenancy()->getUser();

        // we are still passing this to the trait as we want it to be scoped by the active tenant
        if ($user->is_sales || $user->is_supervisor) {
            $query = $query->whereIn('user_id', User::descendantsAndSelf($user->id)->pluck('id'));
        }

        return $this->defaultScopeTenanted($query);
    }

    public function scopeCustomTenanted($query)
    {
        $user = tenancy()->getUser();
        if ($user->is_sales || $user->is_supervisor) {
            return $query->whereIn('user_id', User::descendantsAndSelf($user->id)->pluck('id'));
        }

        // is Director
        return $query->whereIn('channel_id', tenancy()->getTenants($user)->pluck('id'));
    }

    public function scopeMyLeads($query)
    {
        $user = auth()->user();

        if ($user->supervisor_type_id == 3) {
            return $query->whereIn('user_id', $user->getAllChildrenSupervisors()->pluck('id')->all());
        }

        if (!$user->type->in([UserType::DIRECTOR])) return $query->where('user_id', $user->id);
        return;
    }

    public function scopeUnhandled($query)
    {
        return $query->where('is_unhandled', true);
    }

    public function scopeHandled($query)
    {
        return $query->where('is_unhandled', false);
    }

    public function scopeAssignable($query)
    {
        if (!app(CoreService::class)->loggedInUserCanAssignLead()) {
            // dont have access, to assign, remove result
            return $query->whereNull('id');
        }

        // can only assign lead in the same company
        return $query->whereCompanyIds(user()->company_ids);
    }

    public function parent()
    {
        return $this->belongsTo(Lead::class, 'parent_id');
    }

    public function childs()
    {
        return $this->hasMany(Lead::class, 'parent_id');
    }

    public function leadCategory()
    {
        return $this->belongsTo(LeadCategory::class, 'lead_category_id');
    }

    public function subLeadCategory()
    {
        return $this->belongsTo(SubLeadCategory::class, 'sub_lead_category_id');
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'user_id')->where('type', UserType::SALES);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function setNameAttribute($value)
    {
        return $this->label = $value;
    }

    public function getNameAttribute()
    {
        return $this->label;
    }

    public function getIsParentAttribute(): bool
    {
        return is_null($this->parent_id) ? true : false;
    }

    // public function scopeUserName($query, $name)
    // {
    //     return $query->whereHas('user', function ($query) use ($name) {
    //         return $query->where('name', 'LIKE', '%' . $name . '%');
    //     });
    // }

    // public function scopeWhereProductBrandId($query, $id)
    // {
    //     return $query->where('product_brand_id', $id);
    // }

    public function scopeWhereLeadCategoryId($query, $id)
    {
        return $query->where('lead_category_id', $id);
    }

    public function scopeWhereSubLeadCategoryId($query, $id)
    {
        return $query->where('sub_lead_category_id', $id);
    }

    public function scopeCustomerName($query, $name)
    {
        return $query->whereHas('customer', function ($query) use ($name) {
            return $query->whereNameLike($name);
        });
    }

    public function scopeChannelName($query, $name)
    {
        return $query->whereHas('channel', function ($query) use ($name) {
            return $query->where('name', 'LIKE', "%$name%");
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userReferral()
    {
        return $this->belongsTo(User::class, 'user_referral_id');
    }

    // public function productBrand()
    // {
    //     return $this->belongsTo(ProductBrand::class, 'product_brand_id');
    // }

    public function productBrands()
    {
        return $this->belongsToMany(ProductBrand::class, 'product_brand_leads');
    }

    public function getLatestActivityAttribute()
    {
        return $this->leadActivities()->orderBy('id', 'desc')->first();
    }

    public function latestActivity()
    {
        return $this->hasOne(Activity::class, 'lead_id', 'id')->orderBy('id', 'desc');
    }

    public function leadActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'lead_id', 'id');
    }

    public function leadActivityOrders()
    {
        return $this->belongsToMany(Order::class, Activity::class);
    }

    public function leadOrders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeWhereParent($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeWhereChilds($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeWhereCompanyId($query, int $id)
    {
        return $query->whereIn('channel_id', app(CacheService::class)->channelsOfCompany($id)->pluck('id')->all());
    }

    public function scopeWhereCompanyIds($query, array $company_ids = [])
    {
        return $query->whereIn('channel_id', app(CacheService::class)->channelsOfCompanies($company_ids ?? [])->pluck('id')->all());
    }

    public function scopeCustomerSearch($query, $key)
    {
        return $query->whereHas('customer', fn ($q) => $q->whereSearch($key));
    }

    public function closeAsSales(): void
    {
        $this->update(
            [
                'type'   => LeadType::DEAL,
                'status' => LeadStatus::SALES
            ]
        );
    }

    /**
     * Update to next status now and queue next status at the same time (if applicable)
     * @param LeadStatus|null $from_status
     * @return null
     * @throws Exception
     */
    public function nextStatusAndQueue(LeadStatus $from_status = null): void
    {
        // safety check, status may have change while on queue
        if (!is_null($from_status) && $this->status->isNot($from_status)) {
            return;
        }

        // if we have reached the end of status progression
        if (!$next_status = $this->status->nextStatus()) {
            return;
        }

        // check the status due at (this may be removed as a method to cancel status change)
        // or extended when a new queue is placed
        if (is_null($this->status_change_due_at) || $this->status_change_due_at > now()) {
            return;
        }

        // status change
        $dataUpdate = [
            'status'                    => $next_status,
            'status_change_due_at'      => null,
            'has_pending_status_change' => 0,
        ];

        if ($next_status->is(LeadStatus::EXPIRED)) $dataUpdate['type'] = LeadType::DROP;
        $this->update($dataUpdate);

        // if there is still further status progression, we continue queuing for next status
        $this->queueStatusChange();
    }

    /**
     * Queue this lead for next status change (counting time starting now)
     * @throws Exception
     */
    public function queueStatusChange(): void
    {
        // if we have reached the end of status progression
        if (!$next_status = $this->status->nextStatus()) return;

        // $duration = $this->channel->company->getLeadStatusDuration($this->status);

        if ($this->status->in([LeadStatus::SALES, LeadStatus::OTHER_SALES, LeadStatus::EXPIRED])) {
            $duration = 0;
        } else {
            // $duration = $this['options']['lead_status_duration_days'][$this->status->value] ?? config('core.lead_status_duration_days.' . $this->status->value);
            $duration = config('core.lead_status_duration_days.' . $this->status->value);
        }

        if ($duration <= 0) {
            Log::error("Unable to queue Lead status change for Lead id {$this->id}, unknown duration");
            return;
        }

        $dispatch_at = now()->addDays($duration);

        $this->update(
            [
                'status_change_due_at'      => $dispatch_at,
                'has_pending_status_change' => 0
            ]
        );

        LeadStatusChange::dispatch($this, $this->status)->delay($dispatch_at);
    }

    /**
     * Reset status to green if current status
     * allow for status reset.
     */
    public function resetStatus(): void
    {
        if (!$this->status->shouldReset()) {
            return;
        }

        $this->update(['status' => LeadStatus::GREEN]);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function evaluateHasActivity(): void
    {
        if ($this->has_activity) {
            return;
        }

        if (!$this->leadActivities()->exists()) {
            return;
        }

        $this->has_activity = true;
        $this->save();
    }

    /**
     * Whether the customer of this lead has an activity
     * @param $query
     * @param $flag
     * @return mixed
     */
    public function scopeCustomerHasActivity($query, $flag)
    {
        return $query->whereHas('customer', function ($q) use ($flag) {
            return $q->where('has_activity', $flag);
        });
    }

    public function scopeWhereCreatedAtRange($query, $startDate = null, $endDate = null)
    {
        if (is_null($startDate)) $startDate = Carbon::now()->startOfMonth();
        if (is_null($endDate)) $endDate = Carbon::now()->endOfMonth();

        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereDate('leads.created_at', '>=', $startDate);
            $q->whereDate('leads.created_at', '<=', $endDate);
        });
    }

    public function activityBrandValues(): HasMany
    {
        return $this->hasMany(ActivityBrandValue::class);
    }

    public function updateProductBrandsAvailable(array $productBrandIds): void
    {
        if (!$this->is_parent) throw new Exception('only parent leads can update product brand availability');

        $this->productBrands()->updateExistingPivot($productBrandIds, [
            'is_available' => 0
        ]);
    }
}
