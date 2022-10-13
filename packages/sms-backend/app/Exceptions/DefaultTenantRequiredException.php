<?php

namespace App\Exceptions;

/**
 * Class DefaultTenantRequiredException
 * Occurs when requesting tenanted resource, but user
 * does not have default tenant set
 *
 * @package App\Exceptions
 */
class DefaultTenantRequiredException extends CustomApiException
{

}
