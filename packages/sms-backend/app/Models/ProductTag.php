<?php

namespace App\Models;

use App\Interfaces\Tenanted;
use App\Traits\Auditable;
use App\Traits\IsCompanyTenanted;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @mixin IdeHelperProductTag
 */
class ProductTag extends BaseModel implements Tenanted
{
    use SoftDeletes, Auditable, HasSlug, IsCompanyTenanted;

    public $table = 'product_tags';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'slug',
        'company_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                          ->generateSlugsFrom('name')
                          ->saveSlugsTo('slug');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
