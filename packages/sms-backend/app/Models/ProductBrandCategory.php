<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperProductBrandCategory
 */
class ProductBrandCategory extends BaseModel
{
    use SoftDeletes, Auditable;

    public $table = 'product_brand_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'product_brand_id',
        'brand_category_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     *  Setup model event hooks.
     */

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function brandCategory()
    {
        return $this->belongsTo(BrandCategory::class);
    }

    public function productBrandCategory()
    {
        return $this->belongsTo(ProductBrandCategory::class);
    }

    public function brandCategoryBrands()
    {
        return $this->hasMany(Brand::class, 'brand_category_id', 'id');
    }
}
