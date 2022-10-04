<?php

namespace App\Models;

use App\Enums\ProductCategoryType;
use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsCompanyTenanted;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @mixin IdeHelperProductCategory
 */
class ProductCategory extends BaseModel implements HasMedia, Tenanted
{
    use SoftDeletes, CustomInteractsWithMedia, Auditable, HasSlug, NodeTrait, IsCompanyTenanted;

    const TYPE_SELECT = [
        'collection'    => 'Collection',
        'subcollection' => 'Sub Collection',
        'brand-type'    => 'Brand Type',
        'brand'         => 'Brand',
        'category'      => 'Category',
    ];

    public $table = 'product_categories';

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
        'parent_id',
        'type',
        'slug',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'parent_id'  => 'integer',
        'type'       => ProductCategoryType::class
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                          ->generateSlugsFrom('name')
                          ->saveSlugsTo('slug');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function parentProductCategories()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id', 'id');
    }

    public function getPhotoAttribute()
    {
        $file = $this->getMedia('photo')->last();

        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }

        return $file;
    }

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
