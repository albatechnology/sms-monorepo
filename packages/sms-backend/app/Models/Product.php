<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsSubscribedTenanted;
use App\Traits\SaveToSubscriber;
// use App\Traits\IsCompanyTenanted;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\SlugOptions;
use Str;

/**
 * @mixin IdeHelperProduct
 */
class Product extends BaseModel implements HasMedia
{
    use SoftDeletes, CustomInteractsWithMedia, Auditable, SaveToSubscriber, IsSubscribedTenanted;

    public $table = 'products';

    protected $appends = [
        'photo',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        // 'company_id',
        'subscribtion_user_id',
        'product_brand_id',
        'product_category_id',
        'name',
        'price',
        'is_active',
        'tags',
        // 'product_model_id',
        // 'product_version_id',
        // 'product_category_code_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'video_url',
        'description',
    ];

    protected $casts = [
        'price'                    => 'integer',
        // 'company_id'               => 'integer',
        'product_brand_id'         => 'integer',
        // 'product_model_id'         => 'integer',
        // 'product_version_id'       => 'integer',
        // 'product_category_code_id' => 'integer',
        'product_category_id'      => 'integer',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::updated(function (self $model) {
            // if ($model->isDirty('product_brand_id')) {
            //     $brandCategoryIds = $model->brand->productBrandCategories->map(function($p){
            //         return $p->brand_category_id;
            //     })->toArray();

            //     $brandCategoryId = $brandCategoryIds[0] ?? null;
            //     $model->productUnits()->update(['brand_category_id' => $brandCategoryId]);
            // }
        });

        parent::boot();
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = implode(',', $value ?? []);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            // ->generateSlugsFrom(['company_id', 'name'])
            ->generateSlugsFrom(['name'])
            ->saveSlugsTo('code');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function productProductUnits()
    {
        return $this->hasMany(ProductUnit::class, 'product_id', 'id');
    }

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class, 'product_id', 'id');
    }

    public function productsActivities()
    {
        return $this->belongsToMany(Activity::class);
    }

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function productBrand()
    {
        return $this->belongsTo(ProductBrand::class);
    }

    // public function tags()
    // {
    //     return $this->belongsToMany(ProductTag::class, 'product_product_tag');
    // }

    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, 'product_brand_id');
    }

    // public function model()
    // {
    //     return $this->belongsTo(ProductModel::class, 'product_model_id');
    // }

    // public function version()
    // {
    //     return $this->belongsTo(ProductVersion::class, 'product_version_id');
    // }

    // public function categoryCode()
    // {
    //     return $this->category_code();
    // }

    // public function category_code()
    // {
    //     return $this->belongsTo(ProductCategoryCode::class, 'product_category_code_id');
    // }

    public function productListPivot()
    {
        return $this->hasMany(ProductProductList::class, 'product_id');
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

    public function scopeWhereActive($query)
    {
        return $query->where('is_active', 1);
    }

    // public function scopeWhereTags($query, $csv_tags)
    // {
    //     return $query->whereHas('tags', function ($query) use ($csv_tags) {
    //         $query->whereIn('slug', explode(',', $csv_tags));
    //     });
    // }

    public function scopeWhereTags($query, $tag)
    {
        return $query->where('tags', 'like', '%' . $tag . '%');
    }

    public function toRecord()
    {

        $data = $this->loadMissing(['brand'])->toArray();

        unset(
            $data['created_at'],
            $data['updated_at'],
            $data['deleted_at'],
            $data['is_active'],
            // $data['company_id'],
            $data['photo'],
            $data['media'],
            $data["product_brand_id"],
            // $data["product_model_id"],
            // $data["product_version_id"],
            // $data["product_category_code_id"],
            $data["product_category_id"],
        );

        $data['brand']         = $this->brand->toRecord();
        // $data['model']         = $this->model->toRecord();
        // $data['version']       = $this->version->toRecord();
        // $data['category_code'] = $this->category_code->toRecord();

        // unset the nested product

        return $data;
    }

    // public function scopeWhereProductModelId($query, ...$ids)
    // {
    //     return $query->whereIn('product_model_id', $ids);
    // }

    public function scopeWhereProductCategoryId($query, ...$ids)
    {
        return $query->whereIn('product_category_id', $ids);
    }

    public function scopeWhereProductBrandId($query, ...$ids)
    {
        return $query->whereIn('product_brand_id', $ids);
    }

    // public function scopeWhereProductVersionId($query, ...$ids)
    // {
    //     return $query->whereIn('product_version_id', $ids);
    // }

    // public function scopeWhereProductCategoryCodeId($query, ...$ids)
    // {
    //     return $query->whereIn('product_category_code_id', $ids);
    // }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function scopeWhereNameSearch($query, $string)
    {
        $queryString = (string) Str::of($string)
            ->trim()
            ->replace(' ', '%')
            ->append('%')
            ->prepend('%');

        return $query->where('name', 'LIKE', $queryString);
    }
}
