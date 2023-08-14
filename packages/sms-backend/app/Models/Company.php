<?php

namespace App\Models;

use App\Enums\CacheTags;
use App\Enums\LeadStatus;
use App\Interfaces\ReportableScope;
use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\IsSubscribedTenanted;
use DateTimeInterface;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

/**
 * @mixin IdeHelperCompany
 */
class Company extends BaseModel implements Tenanted, HasMedia, ReportableScope
{
    use SoftDeletes, Auditable, InteractsWithMedia, IsSubscribedTenanted;

    public $table = 'companies';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'subscribtion_user_id',
        'name',
        'company_account_id',
        'options',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    protected $casts = [
        'company_account_id' => 'integer',
        'options'            => 'array',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::saved(function (self $model) {
            cache_service()->forget([CacheTags::COMPANY]);
        });

        self::deleted(function (self $model) {
            cache_service()->forget([CacheTags::COMPANY]);
        });

        self::created(function (self $model) {
            $model->companyData()->create([]);
        });

        parent::boot();
    }

    public function companyData()
    {
        return $this->hasOne(CompanyData::class, 'company_id', 'id');
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
        $hasActiveCompany = tenancy()->getActiveCompany();
        $isAdmin          = tenancy()->getUser()->is_admin;

        if ($isAdmin) return $query;
        if ($hasActiveChannel) return $query->where('id', $hasActiveChannel->company->id);
        if (!$isAdmin) return $query->where('id', tenancy()->getUser()->company_id);
        if ($hasActiveCompany) return $query->where('id', $hasActiveCompany->id);

        return $query->whereIn('id', auth()->user()->company_ids ?? []);
        // return $query;
    }

    public function scopeWhereCompanyId($query, int $id)
    {
        return $query->where('id', $id);
    }

    // region Relationship

    public function tenant()
    {
        return null;
    }

    public function newTargets()
    {
        return $this->morphMany(NewTarget::class, 'model');
    }

    public function companyChannels()
    {
        return $this->hasMany(Channel::class, 'company_id', 'id');
    }

    public function companyProducts()
    {
        return $this->hasMany(Product::class, 'company_id', 'id');
    }

    public function companyItems()
    {
        return $this->hasMany(Item::class, 'company_id', 'id');
    }

    public function companyProductCategories()
    {
        return $this->hasMany(ProductCategory::class, 'company_id', 'id');
    }

    public function companyProductTags()
    {
        return $this->hasMany(ProductTag::class, 'company_id', 'id');
    }

    public function companyDiscounts()
    {
        return $this->hasMany(Discount::class, 'company_id', 'id');
    }

    public function companyPromos()
    {
        return $this->hasMany(Promo::class, 'company_id', 'id');
    }

    public function companyBanners()
    {
        return $this->hasMany(Banner::class, 'company_id', 'id');
    }

    public function companyPaymentCategories()
    {
        return $this->hasMany(PaymentCategory::class, 'company_id', 'id');
    }

    public function companyPaymentTypes()
    {
        return $this->hasMany(PaymentType::class, 'company_id', 'id');
    }

    public function companiesUsers()
    {
        return $this->belongsToMany(User::class);
    }

    public function companyAccounts()
    {
        return $this->hasMany(CompanyAccount::class);
    }

    /**
     * Default company account
     * @return BelongsTo
     */
    public function companyAccount(): BelongsTo
    {
        return $this->belongsTo(CompanyAccount::class);
    }

    //endregion

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

        return $user->company_id == $this->id;
    }

    public function registerMediaConversions(SpatieMedia $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function getLogoAttribute()
    {
        $files = $this->getMedia('logo');

        $item = $files->first();
        if ($item) {
            $item->url       = $item->getUrl() ?? null;
            $item->thumbnail = $item->getUrl('thumb') ?? null;
            $item->preview   = $item->getUrl('preview') ?? null;
        }

        return $item;
    }

    /**
     * Get Lead status duration
     * @param LeadStatus $status
     * @return int
     */
    public function getLeadStatusDuration(LeadStatus $status): int
    {
        if ($status->in([LeadStatus::SALES, LeadStatus::OTHER_SALES, LeadStatus::EXPIRED])) {
            return 0;
        }

        return $this['options']['lead_status_duration_days'][$status->value] ??
            config('core.lead_status_duration_days.' . $status->value);
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
}
