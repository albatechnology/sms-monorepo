<?php

namespace App\Exceptions;

/**
 * Class DefaultCompanyAccountDoesNotExistException
 * Occurs when requesting the default company account, but the active company does not have default account set.
 * @package App\Exceptions
 */
class DefaultCompanyAccountDoesNotExistException extends CustomApiException
{

}