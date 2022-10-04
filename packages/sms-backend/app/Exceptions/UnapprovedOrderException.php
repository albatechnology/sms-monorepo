<?php

namespace App\Exceptions;

/**
 * Class UnapprovedOrderException
 * The requested action is invalid due to the relating order being unapproved.
 * @package App\Exceptions
 */
class UnapprovedOrderException extends CustomApiException
{

}