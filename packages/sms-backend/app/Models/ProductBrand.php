<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsSubscribedTenanted;
// use App\Traits\IsCompanyTenanted;
// use App\Traits\ProductListable;
use App\Traits\SaveToSubscriber;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperProductBrand
 */
class ProductBrand extends BaseModel implements HasMedia
{
    use CustomInteractsWithMedia, Auditable, SoftDeletes, SaveToSubscriber, IsSubscribedTenanted;

    public $table = 'product_brands';

    protected $appends = [
        'photo',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'subscribtion_user_id',
        'name',
        // 'company_id',
        'hpp_calculation',
        'currency_id',
        'brand_category_id',
    ];

    protected $casts = [
        // 'company_id' => 'integer',
        'currency_id' => 'integer',
        'hpp_calculation' => 'integer',
        'brand_category_id' => 'integer',
    ];

    public function getPhotoAttribute()
    {
        $files = $this->getMedia('photo');
        $files->each(function ($item) {
            $item->url       = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview   = $item->getUrl('preview');
        });

        return $files;
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function brandCategory()
    {
        return $this->belongsTo(BrandCategory::class, 'brand_category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class);
    }

    public function productBrandLeads()
    {
        return $this->hasMany(ProductBrandLead::class, 'product_brand_id');
    }

    public function colours()
    {
        return $this->hasMany(Colour::class, 'product_brand_id');
    }

    public function toRecord()
    {

        $data = $this->toArray();

        unset(
            $data['created_at'],
            $data['updated_at'],
            $data['deleted_at'],
            // $data['company_id'],
            $data['photo'],
            $data['media'],
        );

        return $data;
    }

    public function productBrandCategories()
    {
        return $this->hasMany(ProductBrandCategory::class);
    }

    public function brandCategories()
    {
        return $this->belongsToMany(BrandCategory::class, ProductBrandCategory::class);
    }

    public function activityBrandValues()
    {
        return $this->hasMany(ActivityBrandValue::class, 'product_brand_id');
    }

    public function activityBrandValuesDeals()
    {
        return $this->hasMany(ActivityBrandValue::class, 'product_brand_id');
    }
}
