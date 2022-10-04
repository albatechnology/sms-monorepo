<?php

namespace App\Exceptions;

use Exception;

/**
 * Class UnhandledTargetTypeException
 * Occurs when certain logic is not implemented for a target type.
 * This is internal error that require developer to add missing implementation.
 *
 * @package App\Exceptions
 */
class UnhandledTargetTypeException extends Exception
{

}