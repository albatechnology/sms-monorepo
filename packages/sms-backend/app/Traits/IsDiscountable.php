<?php


namespace App\Traits;

use App\Enums\DiscountError;
use App\Interfaces\Discountable;
use App\Interfaces\DiscountableLine;
use App\Models\Discount;
use App\Services\OrderService;
use Exception;

trait IsDiscountable
{

    // region SETTER AND GETTERS
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param Discount $discount
     * @throws Exception
     */
    public function setDiscount(Discount $discount)
    {
        OrderService::setDiscount($this, $discount);
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount ?? null;
    }

    public function getOrderDiscounts()
    {
        return $this->order_discounts ?? null;
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class)->withTrashed();
    }

    public function setDiscountError(DiscountError $error)
    {
        $this->discount_error = $error;
    }

    public function updatePricesFromItemLine()
    {
        $item_lines = $this->getDiscountableLines();

        $this->setTotalPrice($item_lines->sum(fn(DiscountableLine $line) => $line->getTotalPrice()));
        $this->setTotalDiscount($item_lines->sum(fn(DiscountableLine $line) => $line->getTotalDiscount()));
    }

    public function setTotalPrice(int $price)
    {
        $this->total_price = $price;
    }

    // endregion

    public function setTotalDiscount(int $price)
    {
        $this->total_discount = $price;
    }

    // summary of total discount harus di unset sebelum di save
    public function setSumTotalDiscount(int $price)
    {
        $this->sum_total_discount = $price + ($this->total_discount ?? 0);
    }

    public function getDiscountError(): ?DiscountError
    {
        return $this?->discount_error;
    }

    /**
     * @throws Exception
     */
    public function resetDiscountPrices()
    {
        $this->checkInstance();

        $this->setTotalPrice($this->getTotalPrice() + $this->getTotalDiscount());
        $this->setTotalDiscount(0);
        $this->setSumTotalDiscount(0);

        if ($this instanceof Discountable) {
            $this->getDiscountableLines()->each(fn(DiscountableLine $line) => $line->resetDiscount());
        }
    }

    /**
     *
     * @throws Exception
     */
    public function resetDiscount()
    {
        $this->checkInstance();
        $this->resetDiscountPrices();

        $this->discount    = null;
        $this->discount_id = null;
    }

    protected function checkInstance()
    {
        if (!$this instanceof Discountable && !$this instanceof DiscountableLine) {
            throw new Exception('Discount applied to non discountable class.');
        }
    }

    public function getTotalPrice(): int
    {
        return $this->total_price ?? 0;
    }

    public function getTotalDiscount(): int
    {
        return $this->total_discount ?? 0;
    }

    public function getSumTotalDiscount(): int
    {
        return $this->sum_total_discount ?? 0;
    }

    public function getAdditionalDiscount(): int
    {
        return 0;
    }
}
