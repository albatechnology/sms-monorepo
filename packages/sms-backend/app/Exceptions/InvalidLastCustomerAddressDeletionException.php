<?php

namespace App\Exceptions;

/**
 * Dont allow deletion of customer's only address
 *
 * Class InvalidLastCustomerAddressDeletionException
 * @package App\Exceptions
 */
class InvalidLastCustomerAddressDeletionException extends BaseException
{
    protected $message = 'Not allowed to delete the last address for a customer.';

}