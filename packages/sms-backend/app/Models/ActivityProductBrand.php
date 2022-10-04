<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ActivityProductBrand extends Pivot
{
    protected $table = "activity_product_brand";

    protected $casts = [
        'estimated_value' => 'integer',
        'order_value' => 'integer',
    ];

    public function productBrandCategories()
    {
        return $this->hasMany(ProductBrandCategory::class, 'product_brand_id', 'product_brand_id');
    }
}
