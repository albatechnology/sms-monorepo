<?php

namespace App\Exceptions;

/**
 * Class ExpectedOrderPriceMismatchException
 *
 * Occurs when an order has different price compared to the expected price.
 * This may be cause by a recently updated product or discount. The app should
 * refresh the cart to fetch latest product prices, and re-apply discount.
 *
 * @package App\Exceptions
 */
class ExpectedOrderPriceMismatchException extends CustomApiException
{

}