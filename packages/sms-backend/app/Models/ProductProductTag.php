<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperProductProductTag
 */
class ProductProductTag extends Pivot
{
    protected $table = 'product_product_tag';
}