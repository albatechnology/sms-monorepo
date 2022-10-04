<?php

namespace App\Enums;

/**
 * @method static static COLLECTION()
 * @method static static SUB_COLLECTION()
 * @method static static BRAND_TYPE()
 * @method static static BRAND()
 * @method static static CATEGORY()
 */
final class ProductCategoryType extends BaseEnum
{
    const COLLECTION     = 1;
    const SUB_COLLECTION = 2;
    const BRAND_TYPE     = 3;
    const BRAND          = 4;
    const CATEGORY       = 5;
}
