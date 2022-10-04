<?php

namespace App\Pipes\Discountable;

use App\Enums\DiscountScope;
use App\Interfaces\Discountable;
use App\Interfaces\DiscountableLine;
use App\Models\Discount;
use App\Services\OrderService;
use Closure;
use Exception;

/**
 * Apply discount to the discountable lines class if applicable
 * Class ResetDiscount
 * @package App\Pipes\Discountable
 */
class CalculateDiscountForDiscountableLineClass
{
    /**
     * @param Discountable $discountable
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Discountable $discountable, Closure $next)
    {
        if (!$discount = $discountable->getDiscount()) return $next($discountable);
        if ($discount->applyToProductUnit()) {

            if ($discount->scope->is(DiscountScope::TYPE)) {
                $discountable = $this->discountScopeType($discountable, $discount);
            } elseif ($discount->scope->is(DiscountScope::CATEGORY)) {
                $discountable = $this->discountScopeCategory($discountable, $discount);
            }
            // elseif ($discount->scope->is(DiscountScope::SECOND_PLACE_BRAND_PRICE)) {
            //     $discountable = $this->discountScopeSecondPlaceBrandPrice($discountable, $discount);
            // }
            else {
                $discountable = $this->discountScopeQuantity($discountable, $discount);
            }

            $discountable->updatePricesFromItemLine();
        }

        return $next($discountable);
    }

    public function checkAllowedProductUnitIdsByProductBrand(Discountable $discountable, Discount $discount)
    {
        $allowedProductUnitIds = $discountable->getDiscountableLines()->filter(function ($line) use ($discount) {
            return $discount->product_brand_id == $line->product_unit->product->brand->id;
        })->pluck('product_unit_id')->toArray();
        return $allowedProductUnitIds;
    }

    /**
     * @param Discountable $discountable
     * @param Discount $discount
     * @return Discountable
     */
    public function discountScopeQuantity(Discountable $discountable, Discount $discount)
    {
        $allowedProductUnitIds = $discountable->order_details->pluck('product_unit_id')->all();
        if (!empty($discount->product_brand_id)) {
            $allowedProductUnitIds = $this->checkAllowedProductUnitIdsByProductBrand($discountable, $discount);
        }
        $discountable->allowed_product_unit_ids = $allowedProductUnitIds;

        $discountable->getDiscountableLines()->each(function (DiscountableLine $line) use ($discount, $allowedProductUnitIds) {
            if (in_array($line->product_unit_id, $allowedProductUnitIds)) {
                $line->setTotalDiscount(OrderService::calculateTotalDiscount($line, $discount));
                $line->setTotalPrice($line->getTotalPrice() - $line->getTotalDiscount());
            }
        });

        return $discountable;
    }

    /**
     * @param Discountable $discountable
     * @param Discount $discount
     * @return Discountable
     */
    public function discountScopeType(Discountable $discountable, Discount $discount)
    {
        // allowed product unit id to give discount. By default all product unit get discount
        // but if this discount have product_unit_ids, only permitted product units can be discounted
        $allowedProductUnitIds = $discountable->order_details->pluck('product_unit_id')->all();
        if (!empty($discount->product_unit_ids)) {
            $allowedProductUnitIds = $discount->product_unit_ids;
        }

        $checkAllowedProductUnitIds = $allowedProductUnitIds;
        if (!empty($discount->product_brand_id)) {
            $checkAllowedProductUnitIds = $this->checkAllowedProductUnitIdsByProductBrand($discountable, $discount);
        }

        $allowedProductUnitIds = array_intersect($checkAllowedProductUnitIds, $allowedProductUnitIds);
        $discountable->allowed_product_unit_ids = $allowedProductUnitIds;

        $discountable->getDiscountableLines()->each(function (DiscountableLine $line) use ($discount, $allowedProductUnitIds) {
            if (in_array($line->product_unit_id, $allowedProductUnitIds)) {
                $line->setTotalDiscount(OrderService::calculateTotalDiscount($line, $discount));
                $line->setTotalPrice($line->getTotalPrice() - $line->getTotalDiscount());
            }
        });

        return $discountable;
    }

    /**
     * @param Discountable $discountable
     * @param Discount $discount
     * @return Discountable
     */
    public function discountScopeCategory(Discountable $discountable, Discount $discount)
    {
        $allowedProductUnitIds = [];
        if ($discount->product_unit_category == null) {
            $discountable->allowed_product_unit_ids = $allowedProductUnitIds;
            return $discountable;
        }

        $allowedProductUnitIds = $discountable->order_details->pluck('product_unit_id')->all();
        if (!empty($discount->product_brand_id)) {
            $allowedProductUnitIds = $this->checkAllowedProductUnitIdsByProductBrand($discountable, $discount);
        }
        $discountable->allowed_product_unit_ids = $allowedProductUnitIds;

        $discountable->getDiscountableLines()->each(function (DiscountableLine $line) use ($discount, $allowedProductUnitIds) {
            if (in_array($line->product_unit_id, $allowedProductUnitIds) && ($discount->product_unit_category == $line->product_unit->product_unit_category)) {
                $line->setTotalDiscount(OrderService::calculateTotalDiscount($line, $discount));
                $line->setTotalPrice($line->getTotalPrice() - $line->getTotalDiscount());
            }
        });

        return $discountable;
    }

    /**
     * @param Discountable $discountable
     * @param Discount $discount
     * @return Discountable
     */
    public function discountScopeSecondPlaceBrandPrice(Discountable $discountable, Discount $discount)
    {
        $allowedProductUnitIds = [];
        if ($discount->product_brand_id == null) {
            $discountable->allowed_product_unit_ids = $allowedProductUnitIds;
            return $discountable;
        }

        // $allowedProductUnits = $discountable->getDiscountableLines()->filter(function (DiscountableLine $line) use ($discount) {
        //     return $discount->product_brand_id == $line->product_unit->product->brand->id;
        // })->sortBy();

        $allowedProductUnits = collect();
        dd($discountable);
        $allowedProductUnits = $discountable->getDiscountableLines()->each(function (DiscountableLine $line) use ($discount, $allowedProductUnits) {
            if ($discount->product_brand_id == $line->product_unit->product->brand->id) {
                $allowedProductUnits->push($line);
            }
        })->sortByDesc('unit_price')->all();

        dd($allowedProductUnits);

        // ->pluck('id')->all();

        if (count($discount->product_brand_id) <= 0) {
            $discountable->allowed_product_unit_ids = $allowedProductUnitIds;
            return $discountable;
        }

        $discountable->getDiscountableLines()->each(function (DiscountableLine $line) use ($discount, $allowedProductUnitIds) {
            if (in_array($line->product_unit_id, $allowedProductUnitIds)) {
                $line->setTotalDiscount(OrderService::calculateTotalDiscount($line, $discount));
                $line->setTotalPrice($line->getTotalPrice() - $line->getTotalDiscount());
            }
        });

        return $discountable;
    }

    // public function discountScopePackage(){
    // }
}
