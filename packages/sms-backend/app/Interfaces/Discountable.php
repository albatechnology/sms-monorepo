<?php


namespace App\Interfaces;

use Illuminate\Support\Collection;

/**
 * Apply to model that has price and is discountable such as
 * cart, cartItemLines, order, orderDetail
 *
 * Interface Discountable
 * @package App\Interfaces
 */
interface Discountable extends DiscountableBase
{
    /**
     * Get collection of discountableLine models from this
     * discountable model
     * @return Collection
     */
    public function getDiscountableLines(): ?Collection;

    public function updatePricesFromItemLine();

    public function getCustomerId(): int;
}
