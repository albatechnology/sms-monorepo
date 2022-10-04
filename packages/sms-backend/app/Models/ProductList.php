<?php

namespace App\Models;

use App\Interfaces\Tenanted;
use App\Traits\IsCompanyTenanted;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperProductList
 */
class ProductList extends BaseModel implements Tenanted
{
    use IsCompanyTenanted;

    protected $fillable = [
        'product_ids', 'model_type', 'model_id'
    ];

    protected $casts = [
        'product_ids' => 'array',
    ];

    const productForeignKeyMap = [
        ProductModel::class => 'product_model_id',
        ProductBrand::class => 'product_brand_id'
    ];

    /**
     *  Setup model event hooks.
     */
    public static function boot()
    {
        self::saved(function (self $model) {
            $model->syncProductList();
        });

        parent::boot();
    }

    // TODO: what happens when the product of a productListable model changes?
    //   for example, a new product as added to the tag. Event maybe the cleanest method.

    /**
     * ProductListable list:
     * - ProductBrand
     * - ProductModel
     * - ProductCategory
     * - ProductTag
     * - Custom & Product
     */

    /**
     * TODO: Maintaining product_product_lists table:
     *
     * ProductTag tables:
     *  - product_tags
     *  - product_product_tags (product_id, product_tag_id)
     *
     *  ## New product added/removed to a product tag
     *  1. New record on product_product_tags, take both product_id, product_tag_id of
     *     the new row from product_product_tags.
     *  2. ProductList where model_type = tags and where model_id = product_tag_id,
     *  3. ProductList->products()->attach(product_id); or detach
     *
     * ## Custom product list
     *  1. Do a direct sync
     *
     * ## Others (product, brand, model, category)
     *  - Not using the  product_product_lists
     */

    /**
     * Update the product_product_list table to
     * this model's setting
     */
    public function updateProductListPivot()
    {
        $model    = $this->productListable;
        $products = $model->products()->get('id');
        $this->products()->sync($products->pluck('id'));
    }

    /**
     * @return MorphTo
     */
    public function model()
    {
        return $this->morphTo('model');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function getProductQuery()
    {
        if ($this->model_type === ProductCategory::class) {
            $categories = ProductCategory::descendantsAndSelf($this->model_id);
            return Product::whereIn('product_category_id', $categories->pluck('id'));
        }

        /**
         * Product list may point directly to a foreign key property in product
         * such as product brand and model
         */
        if (array_key_exists($this->model_type, self::productForeignKeyMap)) {
            $product_column = self::productForeignKeyMap[$this->model_type];
            return Product::where($product_column, $this->model_id);
        }

        /**
         * For everything else, we assume it uses the product_product_list table
         * to hold the relationship of the products contained in a product list
         */
        return Product::whereHas('productListPivot', function ($query) {
            $query->where('product_list_id', $this->model_id);
        });
    }

    /**
     * Sync the product pivot where applicable
     */
    public function syncProductList()
    {
        // Evaluation only needed for custom product list and product tag
        if (!$this->isCustomList() && !$this->model_type !== ProductTag::class) return;

        $product_ids = $this->isCustomList() ? $this->product_ids : $this->getProductQuery()->get('id')->pluck('id')->all();

        $this->products()->sync($product_ids);
    }

    public function isCustomList(): bool
    {
        return is_null($this->model_type) && is_null($this->model_id) && !empty($this->product_ids);
    }

    public function getNameAttribute($value)
    {
        if($value){
            return $value;
        }

        if(is_null('model_type')){
            $count = count($this->product_id);
            return "{$count} products";
        }

        return $this->model->name;
    }
}