<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsCompanyTenanted;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperProductVersion
 */
class ProductVersion extends BaseModel implements HasMedia
{
    use IsCompanyTenanted, CustomInteractsWithMedia, SoftDeletes, Auditable;

    public $table = 'product_versions';

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
        'code',
        'height',
        'length',
        'width',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'details'    => 'array',
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
            $data['company_id'], $data['photo'], $data['media'],
        );

        return $data;
    }
}