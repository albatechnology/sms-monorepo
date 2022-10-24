<?php


namespace App\Interfaces;

use App\Enums\DiscountError;
use App\Models\Discount;

/**
 *
 * Interface DiscountableBase
 * @package App\Interfaces
 */
interface DiscountableBase
{
    public function getId(): ?int;

    public function getQuantity(): int;

    public function getTotalPrice(): int;

    public function setTotalPrice(int $price);

    public function getSumTotalDiscount(): int;
    public function getTotalDiscount(): int;

    public function getAdditionalDiscount(): int;

    public function setTotalDiscount(int $price);

    public function setSumTotalDiscount(int $price);
    public function setDiscount(Discount $discount);

    public function getDiscount(): ?Discount;
    public function getOrderDiscounts();

    public function setDiscountError(DiscountError $error);

    public function getDiscountError(): ?DiscountError;

    /**
     * Reset discount price, total price, and remove discount
     * @return void
     */
    public function resetDiscount();

    /**
     * reset discount prices without actually removing the discount itself
     * @return mixed
     */
    public function resetDiscountPrices();
}
