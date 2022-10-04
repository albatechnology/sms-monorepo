<?php

namespace App\Exceptions;

/**
 * Class UnauthorisedTenantAccessException
 * Occurs when user does not have tenant authority
 * for the requested tenanted resource
 *
 * @package App\Exceptions
 */
class UnauthorisedTenantAccessException extends CustomApiException
{

}