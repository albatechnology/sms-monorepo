<?php

namespace App\Models;

use App\Enums\CacheTags;
use App\Interfaces\ReportableScope;
use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\IsSubscribedTenanted;
use App\Traits\SaveToSubscriber;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperChannel
 */
class Channel extends BaseModel implements ReportableScope, Tenanted
{
    use SoftDeletes, Auditable, SaveToSubscriber, IsSubscribedTenanted;

    public $table = 'channels';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    protected $casts = [
        // 'company_id' => 'integer',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::created(function (self $model) {

            // Create stock of all product units for this channel
            // CoreService::createStocksForChannel($model);
        });

        self::saved(function (self $model) {
            cache_service()->forget([CacheTags::CHANNEL]);
        });

        self::deleted(function (self $model) {
            cache_service()->forget([CacheTags::CHANNEL]);
        });

        parent::boot();
    }

    public function scopeTenanted($query)
    {
        $activeTenant = activeTenant();

        if ($activeTenant) {
            return $query->where('id', activeTenant()->id ?? null);
        } else {
            return $query->whereIn('id', tenancy()->getTenants()->pluck('id'));
        }
    }

    public function scopeCustomTenanted($query)
    {
        return $query->whereIn('id', tenancy()->getTenants()->pluck('id'));
    }

    public function scopeFindTenanted($query, int $id)
    {
        return $query->tenanted()->where('id', $id)->firstOrFail();
    }

    public function subscribtionUser()
    {
        return $this->belongsTo(SubscribtionUser::class);
    }

    public function channelCatalogues()
    {
        return $this->hasMany(Catalogue::class, 'channel_id', 'id');
    }

    public function channelOrders()
    {
        return $this->hasMany(Order::class, 'channel_id', 'id');
    }

    public function channelStocks()
    {
        return $this->hasMany(Stock::class, 'channel_id', 'id');
    }

    public function channelActivities()
    {
        return $this->hasMany(Activity::class, 'channel_id');
    }

    public function grouped_channelActivities()
    {
        return $this->hasMany(Activity::class, 'channel_id')->groupBy('lead_id');
    }

    public function channelLeads()
    {
        return $this->hasMany(Lead::class, 'channel_id', 'id');
    }

    public function activityBrandValues()
    {
        return $this->hasMany(ActivityBrandValue::class, User::class);
    }

    public function channelsUsers()
    {
        return $this->belongsToMany(User::class);
    }

    public function sales()
    {
        return $this->hasMany(User::class)->where('type', 2);
    }

    public function channel_category()
    {
        return $this->belongsTo(ChannelCategory::class, 'channel_category_id');
    }

    public function channelCategory()
    {
        return $this->belongsTo(ChannelCategory::class, 'channel_category_id');
    }

    // public function company()
    // {
    //     return $this->belongsTo(Company::class, 'company_id');
    // }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function targets()
    {
        return $this->morphMany(Target::class, 'model');
    }

    public function newTargets()
    {
        return $this->morphMany(NewTarget::class, 'model');
    }

    public function scopeWhereCompanyId($query, int $id)
    {
        return $query->where('company_id', $id);
    }

    public function getReportLabel(): string
    {
        return $this->name;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function scopeWhereSupervisorTypeLevel($query, int $id)
    {
        return $query;
    }

    public function scopeWhereSupervisorId($query, int $id)
    {
        $user = User::findOrFail($id);
        $channelIds = $user->channelsPivot()->pluck('channel_id')->all();
        return $query->whereIn('id', $channelIds ?? []);
    }
}
