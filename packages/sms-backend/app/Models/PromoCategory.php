<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsCompanyTenanted;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperPromoCategory
 */
class PromoCategory extends BaseModel implements HasMedia
{
    use SoftDeletes, CustomInteractsWithMedia, Auditable, IsCompanyTenanted;

    public $table = 'promo_categories';

    protected $appends = [
        'image',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'description',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
    ];

    public function promos()
    {
        return $this->hasMany(Promo::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
