<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsCompanyTenanted;
use App\Traits\ProductListable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperProductModel
 */
class ProductModel extends BaseModel implements HasMedia
{
    use IsCompanyTenanted, CustomInteractsWithMedia, ProductListable, SoftDeletes, Auditable;

    public $table = 'product_models';

    protected $appends = [
        'photo',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'description',
        'price_min',
        'price_max',
        'need_price_range_update',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'company_id'              => 'integer',
        'price_min'               => 'integer',
        'price_max'               => 'integer',
        'need_price_range_update' => 'boolean',
    ];

    /**
     * Update the price_min and price_max of this product model.
     */
    public function updatePriceRange(): void
    {
        $prices = ProductUnit::query()
            ->whereHas('product.model', function ($query) {
                $query->where('id', $this->id);
            })
            ->get(['price'])
            ->pluck('price');

        $this->update(
            [
                'price_min'               => $prices->min() ?? 0,
                'price_max'               => $prices->max() ?? 0,
                'need_price_range_update' => 0
            ]
        );
    }

    public function scopeWhereProductBrandId($query, ...$ids)
    {
        return $query->whereHas('products', function ($query) use ($ids) {
            $query->whereIn('product_brand_id', $ids)->whereActive();
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

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

    public function toRecord()
    {

        $data = $this->toArray();

        unset(
            $data['created_at'], $data['updated_at'], $data['deleted_at'],
            $data['description'], $data['price_min'], $data['price_max'],
            $data['company_id'], $data['photo'], $data['media'],
            $data['need_price_range_update'],
        );

        return $data;
    }
}