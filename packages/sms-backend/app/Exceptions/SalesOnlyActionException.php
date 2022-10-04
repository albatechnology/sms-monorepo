<?php

namespace App\Exceptions;

/**
 * Class SalesOnlyActionException
 * Occurs when a non sales attempting to perform sales only action
 *
 * @package App\Exceptions
 */
class SalesOnlyActionException extends CustomApiException
{

}