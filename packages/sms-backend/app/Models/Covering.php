<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsCompanyTenanted;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

/**
 * @mixin IdeHelperCovering
 */
class Covering extends BaseModel implements HasMedia
{
    use IsCompanyTenanted, SoftDeletes, Auditable, CustomInteractsWithMedia;

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
        'type',
        'company_id',
        'product_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'product_id' => 'integer',
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

    public function scopeWhereProductId($query, ...$product_ids)
    {
        return $query->whereHas('productUnits.product', function ($query) use ($product_ids) {
            $query->whereIn('id', $product_ids);
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }

    /**
     * Get the properties for record purposes
     */
    public function toRecord(): array
    {
        $data = $this->toArray();

        unset(
            $data['created_at'], $data['updated_at'], $data['deleted_at'],
            $data['company_id'], $data['photo'], $data['media'],
        );

        return $data;
    }
}