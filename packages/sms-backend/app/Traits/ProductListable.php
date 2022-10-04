<?php


namespace App\Traits;

use App\Models\ProductList;

/**
 * Apply to model that can be morphed from product list
 *
 * Trait HasProductList
 * @package App\Traits
 */
trait ProductListable
{
    public function productLists()
    {
        return $this->morphMany(ProductList::class, 'model');
    }
}
