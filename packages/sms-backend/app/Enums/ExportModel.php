<?php

namespace App\Enums;

use App\Models\Colour;
use App\Models\Covering;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategoryCode;
use App\Models\ProductModel;
use App\Models\ProductUnit;
use App\Models\ProductVersion;

/**
 * @method static static PRODUCT()
 * @method static static PRODUCT_BRAND()
 * @method static static PRODUCT_MODEL()
 * @method static static PRODUCT_VERSION()
 * @method static static PRODUCT_CATEGORY_CODE()
 * @method static static PRODUCT_UNIT()
 * @method static static COLOUR()
 * @method static static COVERING()
 */
final class ExportModel extends BaseEnum
{
    const PRODUCT               = Product::class;
    const PRODUCT_BRAND         = ProductBrand::class;
    const PRODUCT_MODEL         = ProductModel::class;
    const PRODUCT_VERSION       = ProductVersion::class;
    const PRODUCT_CATEGORY_CODE = ProductCategoryCode::class;
    const PRODUCT_UNIT          = ProductUnit::class;
    const COLOUR                = Colour::class;
    const COVERING              = Covering::class;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }
}