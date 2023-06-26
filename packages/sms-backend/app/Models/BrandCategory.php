<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\IsSubscribedTenanted;
use App\Traits\SaveToSubscriber;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperBrandCategory
 */
class BrandCategory extends BaseModel
{
    use SoftDeletes, Auditable, SaveToSubscriber, IsSubscribedTenanted;

    public $table = 'brand_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'subscribtion_user_id',
        'name',
        'code',
        'slug',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::created(function (self $model) {
            if (empty($model->code)) {
                $model->update(["code" => sprintf("BC%03d", $model->id)]);
            }
        });

        parent::boot();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function brandCategoryBrands()
    {
        return $this->hasMany(Brand::class, 'brand_category_id', 'id');
    }

    public function revenue()
    {
        return $this->hasMany(ProductBrandCategory::class)
            ->join('activity_product_brand', 'product_brand_categories.product_brand_id', 'activity_product_brand.product_brand_id')
            ->join('activities', 'activity_product_brand.activity_id', 'activities.id')
            ->selectRaw('sum(activities.estimated_value) as amount');
    }
}
