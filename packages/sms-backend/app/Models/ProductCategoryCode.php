<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\IsCompanyTenanted;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperProductCategoryCode
 */
class ProductCategoryCode extends BaseModel
{
    use IsCompanyTenanted, SoftDeletes, Auditable;

    public $table = 'product_category_codes';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
    ];

    public function scopeWhereProductModelId($query, ...$ids)
    {
        return $query->whereHas('products', function ($query) use ($ids) {
            $query->whereIn('product_model_id', $ids)->whereActive();
        });
    }

    public function scopeWhereProductBrandId($query, ...$ids)
    {
        return $query->whereHas('products', function ($query) use ($ids) {
            $query->whereIn('product_brand_id', $ids)->whereActive();
        });
    }

    public function scopeWhereProductVersionId($query, ...$ids)
    {
        return $query->whereHas('products', function ($query) use ($ids) {
            $query->whereIn('product_version_id', $ids)->whereActive();
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function toRecord()
    {

        $data = $this->toArray();

        unset(
            $data['created_at'], $data['updated_at'], $data['deleted_at'],
            $data['company_id'],
        );

        return $data;
    }
}