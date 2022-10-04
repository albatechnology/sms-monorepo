<?php


namespace App\Interfaces;

/**
 * Apply to model that is the product line of a discountable
 * model such as cartItemLines
 *
 * Interface DiscountableLine
 * @package App\Interfaces
 */
interface DiscountableLine extends DiscountableBase
{
    public function getProductUnitId(): int;

    public function setTotalCascadedDiscount(int $price);
}
