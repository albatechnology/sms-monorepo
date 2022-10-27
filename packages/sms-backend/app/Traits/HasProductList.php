<?php


namespace App\Traits;

use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductList;
use App\Models\ProductModel;
use Exception;

/**
 * Apply to model that uses product list to manage relation to products.
 * For example, Discount uses this to refer to products that the discount
 * is applied to.
 *
 * Trait HasProductList
 * @package App\Traits
 */
trait HasProductList
{
    public function productList()
    {
        return $this->belongsTo(ProductList::class);
    }

    /**
     * Determine whether this list cover the given product id
     * @param int $product_id
     * @return bool
     */
    public function applyToProductId(int $product_id): bool
    {
        if (!$this->product_list_id) return true;

        return $this->getProductsQueryFromList()->where('id', $product_id)->count() > 0;
    }

    public function getProductIdsAttribute()
    {
        if (!$this->product_list_id) {
            $this->product_list_id = $this->getProductsQueryFromList()->get('id')->pluck('id');
        }

        return $this->product_list_id;
    }

    public function getProductForeignKeyMap(): array
    {
        return [
            ProductModel::class => 'product_model_id',
            ProductBrand::class => 'product_brand_id'
        ];
    }

    /**
     * The the products related to this model based on the saved product list.
     *
     *
     * @return mixed
     * @throws Exception
     */
    public function productRelationFromList(): mixed
    {
        $list = $this->productList;

        if (empty($list)) return null;

        /**
         * For category, we have to include all the child category of this category.
         * I dont know a way to get this done in relation, so we will use query builder instead
         */
        if ($list->model_type === ProductCategory::class) {
            $categories = ProductCategory::descendantsAndSelf($list->model_id);
            return Product::whereIn('product_category_id', $categories->pluck('id'));
        }

        /**
         * Product list may point directly to a foreign key property in product
         * such as product brand and model
         */
        if (array_key_exists($list->model_type, $this->getProductForeignKeyMap())) {

            return $this->hasManyThrough(
                Product::class,
                ProductList::class,
                'model_id',                                          // Foreign key on the ProductList table
                $this->getProductForeignKeyMap()[$list->model_type], // Foreign key on the Product table
                'id',                                                // Local key on the implementing (e.g., discount) table
                'id'                                                 // Local key on the ProductList table
            );

        }

        /**
         * For everything else, we assume it uses the product_product_list table
         * to hold the relationship of the products contained in a product list
         *
         * This actually skips the product_lists table:
         * Implementing model -> product_product_list -> products
         *
         */
        return $this->belongsToMany(Product::class,
            'product_product_list',
            'product_list_id',
            'product_id',
            'product_list_id'
        );
    }

    public function getProductsQueryFromList()
    {
        if (empty($list = $this->productList)) return null;

        return $list->getProductQuery();
    }
}
