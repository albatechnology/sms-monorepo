<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsCompanyTenanted;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperPromo
 */
class Promo extends BaseModel implements HasMedia
{
    use SoftDeletes, CustomInteractsWithMedia, Auditable, IsCompanyTenanted;

    public $table = 'promos';

    protected $appends = [
        'image',
    ];

    protected $dates = [
        'start_time',
        'end_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'promo_category_id',
        'name',
        'description',
        'start_time',
        'end_time',
        'lead_category_id',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'lead_category_id' => 'integer',
        'company_id' => 'integer',
        'promo_category_id' => 'integer',
    ];

    public function leadCategory()
    {
        return $this->belongsTo(LeadCategory::class, 'lead_category_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function promoCategory()
    {
        return $this->belongsTo(PromoCategory::class, 'promo_category_id');
    }

    public function discount()
    {
        return $this->hasOne(Discount::class);
    }

    public function scopeTargetDatetime($query, $datetime)
    {
        return $query->where('start_time', '<=', Carbon::parse($datetime))
            ->where('end_time', '>=', Carbon::parse($datetime));
    }

    public function scopeStartAfter($query, $datetime)
    {
        return $query->where('start_time', '>=', Carbon::parse($datetime));
    }

    public function scopeEndBefore($query, $datetime)
    {
        return $query->where('end_time', '<=', Carbon::parse($datetime));
    }
}
