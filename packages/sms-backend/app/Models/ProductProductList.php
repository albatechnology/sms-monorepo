<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperProductProductList
 */
class ProductProductList extends Pivot
{
    protected $table = 'product_product_list';

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function productLists(): HasMany
    {
        return $this->hasMany(ProductList::class);
    }
}