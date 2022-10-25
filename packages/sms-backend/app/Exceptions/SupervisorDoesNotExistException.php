<?php

namespace App\Exceptions;

/**
 * Class SupervisorDoesNotExistException
 * Occurs when requesting the supervisor detail of a user that does not have a supervisor.
 * @package App\Exceptions
 */
class SupervisorDoesNotExistException extends CustomApiException
{

}
