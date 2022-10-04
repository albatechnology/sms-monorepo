<?php


namespace Tests\Helpers;


use App\Models\ProductUnit;

/**
 * Class TestCartLine
 * @package Tests\Helpers\
 */
class TestCartLine
{
    public function __construct(public ProductUnit $productUnit, public int $quantity = 1)
    {

    }

    public static function make(ProductUnit $productUnit, int $quantity = 1): static
    {
        return new self($productUnit, $quantity);
    }
}