<?php

namespace App\Models;

use App\Enums\ActivityFollowUpMethod;
use App\Enums\ActivityStatus;
use App\Exceptions\LeadIsUnassignedException;
use App\Interfaces\Reportable;
use App\Interfaces\Tenanted;
use App\Jobs\QueueActivityReminder;
use App\Services\ReportService;
use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsTenanted;
use Carbon\Carbon;
use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperActivity
 */
class Activity extends BaseModel implements Tenanted, Reportable, HasMedia
{
    use SoftDeletes, Auditable, IsTenanted, CustomInteractsWithMedia;

    use IsTenanted {
        IsTenanted::scopeTenanted as protected defaultScopeTenanted;
    }

    const STATUS_SELECT = [
        'hot'    => 'Hot',
        'warm'   => 'Warm',
        'cold'   => 'Cold',
        'closed' => 'CLOSED',
    ];

    public $table = 'activities';

    protected $appends = [
        'image',
    ];

    protected $dates = [
        'follow_up_datetime',
        'created_at',
        'updated_at',
        'deleted_at',
        'reminder_datetime',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'parent_id'                  => 'integer',
        'user_id'                    => 'integer',
        'lead_id'                    => 'integer',
        'channel_id'                 => 'integer',
        // 'interior_design_id'         => 'integer',
        'latest_activity_comment_id' => 'integer',
        'activity_comment_count'     => 'integer',
        'estimated_value'            => 'integer',
        'reminder_sent'              => 'boolean',
        'follow_up_datetime'         => 'datetime',
        'reminder_datetime'          => 'datetime',
        'status'                     => ActivityStatus::class,
        'follow_up_method'           => ActivityFollowUpMethod::class,
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::creating(function (self $model) {

            if ($model->lead->is_unhandled) {
                throw new LeadIsUnassignedException();
            }

            if (empty($model->customer_id)) $model->customer_id = $model->lead->customer_id;
            if (empty($model->channel_id)) $model->channel_id = $model->lead->channel_id;
        });

        self::saved(function (self $model) {
            if (!empty($model->reminder_datetime) && $model->getOriginal('reminder_datetime') != $model->reminder_datetime) {
                QueueActivityReminder::dispatch($model)->delay($model->reminder_datetime);
            }
        });

        self::created(function (self $model) {
            // reset the lead status to green
            $model->lead->resetStatus();

            // register new activity to report
            // app(ReportService::class)->registerTargetMap($model);

            // set lead to has activity
            $lead = $model->lead;
            if (!$lead->has_activity) {
                $lead->has_activity = true;
            }
            $lead->last_activity_status = $model->status;
            $lead->save();

            // set customer to has activity
            $customer = $model->customer;
            if (!$customer->has_activity) {
                $customer->has_activity = true;
                $customer->save();
            }
        });

        parent::boot();
    }

    public static function createForOrder(Order $order, ?int $activityId = null): self
    {
        return self::create(
            [
                'parent_id'          => $activityId,
                'order_id'           => $order->id,
                'lead_id'            => $order->lead_id,
                'customer_id'        => $order->customer_id,
                'channel_id'         => $order->channel_id,
                'user_id'            => $order->user_id,
                // 'interior_design_id' => $order->interior_design_id ?? null,
                'follow_up_method'   => ActivityFollowUpMethod::NEW_ORDER(),
                'status'             => ActivityStatus::HOT(),
                'follow_up_datetime' => now()
            ]
        );
    }

    /**
     * Override default tenanted trait.
     * Sales is scoped to its own activity.
     * Supervisor is scoped to the activity of its children sales.
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

        if ($user->is_sales || $user->is_supervisor) {
            return in_array($this->user_id, User::descendantsAndSelf($user->id)->pluck('id')->all(), false);
        }

        return false;
    }

    public function activityActivityComments()
    {
        return $this->hasMany(ActivityComment::class, 'activity_id', 'id');
    }

    public function child()
    {
        return $this->hasOne(self::class, 'parent_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // public function interiorDesign()
    // {
    //     return $this->belongsTo(InteriorDesign::class, 'interior_design_id');
    // }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function latestComment()
    {
        return $this->belongsTo(ActivityComment::class, 'latest_activity_comment_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getFollowUpDatetimeAttribute($value)
    {
        if (!$value) return null;

        $value = Carbon::createFromFormat('Y-m-d H:i:s', $value);
        return is_api() ? $value->toISOString() : $value->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    }

    public function setFollowUpDatetimeAttribute($value)
    {
        //if(!is_api()) $value = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
        $this->attributes['follow_up_datetime'] = $value ? Carbon::parse($value) : null;
    }

    // public function getCompanyIdAttribute(): int
    // {
    //     return app(CacheService::class)->companyOfChannel($this->channel_id)->id;
    // }

    public function scopeWhereUserId($query, ...$userIds)
    {
        return $query->whereIn('user_id', $userIds);
    }

    public function scopeWhereCustomerId($query, ...$customerIds)
    {
        return $query->whereIn('customer_id', $customerIds);
    }

    /**
     * Activity does not have target id, but we user target
     * to get the user_id, and date range of the target report
     * @param $query
     * @param $targetId
     * @return mixed
     */
    public function scopeWhereTargetId($query, $targetId)
    {
        $target = Target::findOrFail($targetId);

        if ($target->type->getBaseModel() === __CLASS__) {
            return $query->whereHas('targetMaps', fn ($q) => $q->where('target_id', $targetId));
        }

        if ($target->type->getBaseModel() === Order::class) {
            return $query->whereHas('order.targetMaps', fn ($q) => $q->where('target_id', $targetId));
        }

        return $query;
    }

    public function scopeWhereChannelId($query, ...$channelIds)
    {
        return $query->whereIn('channel_id', $channelIds);
    }

    // public function scopeWhereCompanyId($query, $companyId)
    // {
    //     $channelIds = app(CacheService::class)->channelsOfCompany((int)$companyId)->pluck('id');
    //     return $query->whereIn('channel_id', $channelIds->all());
    // }

    public function scopeFollowUpDatetimeBefore($query, $datetime)
    {
        return $query->whereDate('follow_up_datetime', '<=', date('Y-m-d', strtotime($datetime)));
    }

    public function scopeFollowUpDatetimeAfter($query, $datetime)
    {
        return $query->whereDate('follow_up_datetime', '>=', date('Y-m-d', strtotime($datetime)));
        // return $query->where('follow_up_datetime', '>=', Carbon::parse($datetime));
    }

    public function scopeCreatedAfter($query, $datetime)
    {
        return $query->where('created_at', '>=', Carbon::parse($datetime));
    }

    public function scopeCreatedBefore($query, $datetime)
    {
        return $query->where('created_at', '<=', Carbon::parse($datetime));
    }

    /**
     * Scope to activity that have or does not have payment
     * against its order
     * @param $query
     * @param bool $bool
     * @return mixed
     */
    public function scopeWhereHasPayment($query, $bool = true): mixed
    {
        if ($bool) {
            return $query->whereHas('order.orderPayments', fn ($q) => $q->where('status', \App\Enums\PaymentStatus::APPROVED))->has('order.orderPayments');
        }

        return $query->whereDoesntHave('order.orderPayments');
    }

    /**
     * Refresh saved data for latest comment and comment count.
     */
    public function refreshCommentStats(): bool
    {
        $comments = $this->comments()->orderByDesc('id')->get('id');
        return $this->update(
            [
                'latest_activity_comment_id' => $comments->first()?->id,
                'activity_comment_count'     => $comments->count(),
            ]
        );
    }

    public function comments()
    {
        return $this->hasMany(ActivityComment::class, 'activity_id', 'id');
    }

    public function brands()
    {
        return $this->belongsToMany(ProductBrand::class);
        // return $this->belongsToMany(ProductBrand::class)->withPivot(['estimated_value','order_value']);
    }

    /**
     * Get all target map
     */
    public function targetMaps()
    {
        return $this->morphMany(TargetMap::class, 'model');
    }

    /**
     * Whether this activity has an order with product of any of the
     * given brand ids
     * @param $query
     * @param mixed ...$ids
     * @return mixed
     */
    public function scopeWhereHasAnyBrands($query, ...$ids)
    {
        return $query->whereHas('order.order_details.product', function ($q) use ($ids) {
            return $q->whereIn('product_brand_id', $ids);
        });
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function activityProductBrands()
    {
        return $this->hasMany(ActivityProductBrand::class);
    }

    public function activityBrandValues()
    {
        return $this->hasMany(ActivityBrandValue::class);
    }

    public function activity_brand_values()
    {
        return $this->hasMany(ActivityBrandValue::class, 'activity_id', 'id');
    }

    public function scopeWhereCreatedAtRange($query, $startDate = null, $endDate = null)
    {
        if (is_null($startDate)) $startDate = Carbon::now()->startOfMonth();
        if (is_null($endDate)) $endDate = Carbon::now()->endOfMonth();

        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereDate('created_at', '>=', $startDate);
            $q->whereDate('created_at', '<=', $endDate);
        });
    }
}
