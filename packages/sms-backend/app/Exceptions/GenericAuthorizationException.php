<?php

namespace App\Exceptions;

use App\Contracts\Errors;

class GenericAuthorizationException extends CustomApiException
{
    public function getErrorContract()
    {
        return array_merge(Errors::getErrorByException(self::class),
            [
                "label" => $this->getMessage()
            ]
        );
    }
}