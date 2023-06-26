<?php

namespace App\Models;

// use App\Interfaces\Tenanted;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\SaveToSubscriber;
// use App\Traits\IsCompanyTenanted;
use App\Traits\IsSubscribedTenanted;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperProductCategory
 */
class ProductCategory extends BaseModel implements HasMedia
{
    use IsSubscribedTenanted, SoftDeletes, CustomInteractsWithMedia, SaveToSubscriber;

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
        'subscribtion_user_id',
        'name',
        'description',
        // 'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        // 'company_id' => 'integer',
    ];

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
}
