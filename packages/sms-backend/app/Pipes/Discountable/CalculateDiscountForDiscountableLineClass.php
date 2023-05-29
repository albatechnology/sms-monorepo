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
        if ($discount->applyToProduct()) {

            if ($discount->scope->is(DiscountScope::TYPE)) {
                $discountable = $this->discountScopeType($discountable, $discount);
            }
            // elseif ($discount->scope->is(DiscountScope::CATEGORY)) {
            //     $discountable = $this->discountScopeCategory($discountable, $discount);
            // }
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

    public function checkAllowedProductIdsByProductBrand(Discountable $discountable, Discount $discount)
    {
        $allowedProductIds = $discountable->getDiscountableLines()->filter(function ($line) use ($discount) {
            return $discount->product_brand_id == $line->product->brand->id;
        })->pluck('product_id')->toArray();
        return $allowedProductIds;
    }

    /**
     * @param Discountable $discountable
     * @param Discount $discount
     * @return Discountable
     */
    public function discountScopeQuantity(Discountable $discountable, Discount $discount)
    {
        $allowedProductIds = $discountable->order_details->pluck('product_id')->all();
        if (!empty($discount->product_brand_id)) {
            $allowedProductIds = $this->checkAllowedProductIdsByProductBrand($discountable, $discount);
        }
        $discountable->allowed_product_ids = $allowedProductIds;

        $discountable->getDiscountableLines()->each(function (DiscountableLine $line) use ($discount, $allowedProductIds) {
            if (in_array($line->product_id, $allowedProductIds)) {
                $line->setSumTotalDiscount(OrderService::calculateTotalDiscount($line, $discount));
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
        // but if this discount have product_ids, only permitted product units can be discounted
        $allowedProductIds = $discountable->order_details->pluck('product_id')->all();
        if (!empty($discount->product_ids)) {
            $allowedProductIds = $discount->product_ids;
        }

        $checkAllowedProductIds = $allowedProductIds;
        if (!empty($discount->product_brand_id)) {
            $checkAllowedProductIds = $this->checkAllowedProductIdsByProductBrand($discountable, $discount);
        }

        $allowedProductIds = array_intersect($checkAllowedProductIds, $allowedProductIds);
        $discountable->allowed_product_ids = $allowedProductIds;

        $discountable->getDiscountableLines()->each(function (DiscountableLine $line) use ($discount, $allowedProductIds) {
            if (in_array($line->product_id, $allowedProductIds)) {
                $line->setSumTotalDiscount(OrderService::calculateTotalDiscount($line, $discount));
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
        $allowedProductIds = [];
        if ($discount->product_category == null) {
            $discountable->allowed_product_ids = $allowedProductIds;
            return $discountable;
        }

        $allowedProductIds = $discountable->order_details->pluck('product_id')->all();
        if (!empty($discount->product_brand_id)) {
            $allowedProductIds = $this->checkAllowedProductIdsByProductBrand($discountable, $discount);
        }
        $discountable->allowed_product_ids = $allowedProductIds;

        $discountable->getDiscountableLines()->each(function (DiscountableLine $line) use ($discount, $allowedProductIds) {
            if (in_array($line->product_id, $allowedProductIds) && ($discount->product_category == $line->product->product_category)) {
                $line->setSumTotalDiscount(OrderService::calculateTotalDiscount($line, $discount));
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
        $allowedProductIds = [];
        if ($discount->product_brand_id == null) {
            $discountable->allowed_product_ids = $allowedProductIds;
            return $discountable;
        }

        // $allowedProducts = $discountable->getDiscountableLines()->filter(function (DiscountableLine $line) use ($discount) {
        //     return $discount->product_brand_id == $line->product->brand->id;
        // })->sortBy();

        $allowedProducts = collect();
        dd($discountable);
        $allowedProducts = $discountable->getDiscountableLines()->each(function (DiscountableLine $line) use ($discount, $allowedProducts) {
            if ($discount->product_brand_id == $line->product->brand->id) {
                $allowedProducts->push($line);
            }
        })->sortByDesc('unit_price')->all();

        dd($allowedProducts);

        // ->pluck('id')->all();

        if (count($discount->product_brand_id) <= 0) {
            $discountable->allowed_product_ids = $allowedProductIds;
            return $discountable;
        }

        $discountable->getDiscountableLines()->each(function (DiscountableLine $line) use ($discount, $allowedProductIds) {
            if (in_array($line->product_id, $allowedProductIds)) {
                $line->setSumTotalDiscount(OrderService::calculateTotalDiscount($line, $discount));
                $line->setTotalDiscount(OrderService::calculateTotalDiscount($line, $discount));
                $line->setTotalPrice($line->getTotalPrice() - $line->getTotalDiscount());
            }
        });

        return $discountable;
    }
}
