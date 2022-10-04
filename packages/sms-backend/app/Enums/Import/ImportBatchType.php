<?php

namespace App\Enums\Import;

use App\Enums\BaseEnum;
use App\Imports\ColourImport;
use App\Imports\CoveringImport;
use App\Imports\CustomerImport;
use App\Imports\ProductBrandImport;
use App\Imports\ProductCategoryCodeImport;
use App\Imports\ProductImport;
use App\Imports\ProductModelImport;
use App\Imports\ProductUnitImport;
use App\Imports\ProductVersionImport;
use App\Imports\StockImport;
use App\Imports\InteriorDesignImport;
use App\Imports\LeadImport;
use App\Models\Colour;
use App\Models\Covering;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategoryCode;
use App\Models\ProductModel;
use App\Models\ProductUnit;
use App\Models\ProductVersion;
use App\Models\Stock;
use App\Models\InteriorDesign;
use App\Models\Lead;

/**
 * @method static static PRODUCT_BRAND()
 * @method static static PRODUCT_MODEL()
 * @method static static PRODUCT_VERSION()
 * @method static static PRODUCT_CATEGORY_CODE()
 * @method static static PRODUCT()
 * @method static static PRODUCT_UNIT()
 * @method static static COVERING()
 * @method static static COLOUR()
 * @method static static STOCK()
 * @method static static CUSTOMER()
 * @method static static INTERIOR_DESIGN()
 */
final class ImportBatchType extends BaseEnum
{
    const PRODUCT_BRAND         = 0;
    const PRODUCT_MODEL         = 1;
    const PRODUCT_VERSION       = 2;
    const PRODUCT_CATEGORY_CODE = 3;
    const PRODUCT               = 4;
    const PRODUCT_UNIT          = 5;
    const COVERING              = 6;
    const COLOUR                = 7;
    const STOCK                 = 8;
    const CUSTOMER              = 9;
    const INTERIOR_DESIGN       = 10;
    const LEAD = 11;

    public static function getDescription($value): string
    {
        return match ($value) {
            default => self::getKey($value),
        };
    }

    public function getImporter(): ?string
    {
        return match ($this->value) {
            self::PRODUCT_BRAND => ProductBrandImport::class,
            self::PRODUCT_MODEL => ProductModelImport::class,
            self::PRODUCT_VERSION => ProductVersionImport::class,
            self::PRODUCT_CATEGORY_CODE => ProductCategoryCodeImport::class,
            self::PRODUCT => ProductImport::class,
            self::PRODUCT_UNIT => ProductUnitImport::class,
            self::COVERING => CoveringImport::class,
            self::COLOUR => ColourImport::class,
            self::STOCK => StockImport::class,
            self::CUSTOMER => CustomerImport::class,
            self::INTERIOR_DESIGN => InteriorDesignImport::class,
            self::LEAD => LeadImport::class,
            default => null,
        };
    }

    public function getModel(): string
    {
        return match ($this->value) {
            self::PRODUCT_BRAND => ProductBrand::class,
            self::PRODUCT_MODEL => ProductModel::class,
            self::PRODUCT_VERSION => ProductVersion::class,
            self::PRODUCT_CATEGORY_CODE => ProductCategoryCode::class,
            self::PRODUCT => Product::class,
            self::PRODUCT_UNIT => ProductUnit::class,
            self::COVERING => Covering::class,
            self::COLOUR => Colour::class,
            self::STOCK => Stock::class,
            self::CUSTOMER => Customer::class,
            self::INTERIOR_DESIGN => InteriorDesign::class,
            self::LEAD => Lead::class,
            default => null,
        };
    }
}
