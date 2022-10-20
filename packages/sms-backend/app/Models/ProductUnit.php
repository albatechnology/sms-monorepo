<?php

namespace App\Models;

use App\Enums\ProductCategory;
use App\Services\CoreService;
use App\Traits\Auditable;
use App\Traits\CustomInteractsWithMedia;
use App\Traits\IsCompanyTenanted;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\HasMedia;
use Str;

/**
 * @mixin IdeHelperProductUnit
 */
class ProductUnit extends BaseModel implements HasMedia
{
    use SoftDeletes, CustomInteractsWithMedia, Auditable, IsCompanyTenanted;

    public $table = 'product_units';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'photo',
    ];

    protected $fillable = [
        'product_id',
        'name',
        'description',
        'information',
        'detail',
        'price',
        'purchase_price',
        'calculated_hpp',
        'production_cost',
        'is_active',
        'sku',
        'volume',
        'colour_id',
        'covering_id',
        'company_id',
        'product_unit_category',
        'brand_category_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'price'           => 'integer',
        'purchase_price'  => 'integer',
        'calculated_hpp' => 'integer',
        'production_cost' => 'integer',
        'product_id'      => 'integer',
        'colour_id'       => 'integer',
        'covering_id'     => 'integer',
        'company_id'      => 'integer',
        'product_unit_category' => ProductCategory::class,
        'brand_category_id' => 'integer',
        'volume' => 'float',
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        parent::boot();
        self::created(function (self $model) {
            // Create stock of all channel
            CoreService::createStocksForProductUnit($model);
        });

        self::saving(function (self $model) {
            $brandCategoryIds = Product::findOrFail($model->product_id)->brand->productBrandCategories->map(function ($p) {
                return $p->brand_category_id;
            })->toArray();
            $model->brand_category_id = $brandCategoryIds[0] ?? null;

            if (!empty($model->purchase_price) && $model->isDirty('purchase_price')) {
                self::calculatingHPP($model);
            }

            $model->sku = trim($model->sku);
        });

        self::saved(function (self $model) {
            if (!empty($model->price) && $model->isDirty('price')) {
                $model->product->model->updatePriceRange();
            }
        });
    }

    public function setSkuAttribute($value)
    {
        $this->attributes['sku'] = trim($value);
    }

    /**
     * Calculating product unit calculated_hpp
     */
    public static function calculatingHPP(self $model): void
    {
        $productBrand = $model->product->brand;

        $productBrandHppCalculation = $productBrand->hpp_calculation;
        $currency = $productBrand->currency;
        if ($currency && $productBrandHppCalculation != 0) {
            $calculatedHpp = ($model->purchase_price * $currency->value) + ($model->purchase_price * ($productBrandHppCalculation / 100) * $currency->value);
            $model->calculated_hpp = $calculatedHpp ?? 0;
        }
    }

    public function productUnitItemProductUnits()
    {
        return $this->hasMany(ItemProductUnit::class, 'product_unit_id', 'id');
    }

    public function productUnitOrderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_unit_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function colour()
    {
        return $this->belongsTo(Colour::class);
    }

    public function covering()
    {
        return $this->belongsTo(Covering::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function getPriceAttribute($price)
    {
        if (is_null($price)) {
            $productPrice = $this->product->price;
            if (is_null($productPrice)) {
                Log::critical("Product unit {$this->id} does not have a price!");
            } else {
                ProductUnit::where('id', $this->id)->update(['price' => $productPrice]);
                $price = $productPrice;
            }
        }

        return $price;
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
        return $query->where('is_active', true);
    }

    /**
     * Get the properties for record purposes
     */
    public function toRecord(): array
    {
        $data = $this->loadMissing(['colour', 'covering'])->toArray();

        unset(
            $data['created_at'],
            $data['updated_at'],
            $data['deleted_at'],
            $data['description'],
            $data['product'],
            $data['colour_id'],
            $data['covering_id'],
            $data['is_active'],
        );

        $data['colour']   = $this->colour->toRecord();
        $data['covering'] = $this->covering->toRecord();

        return $data;
    }

    public function updateProductBrandPriceRange()
    {
        $this->product->model->updatePriceRange();
    }

    public function scopeWhereNameSearch($query, $string)
    {
        $queryString = (string)Str::of($string)
            ->trim()
            ->replace(' ', '%')
            ->append('%')
            ->prepend('%');

        return $query->where('name', 'LIKE', $queryString);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
