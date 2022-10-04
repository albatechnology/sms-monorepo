<?php

namespace App\Enums;

/**
 * @method static static ORDER()
 * @method static static PRODUCT()
 */
final class DiscountableType extends BaseEnum
{
    const ORDER   = "order";
    const PRODUCT = "product";
}