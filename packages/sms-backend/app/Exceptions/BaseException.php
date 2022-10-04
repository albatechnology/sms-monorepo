<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\MessageBag;

/**
 * Class BaseException
 * @package App\Exceptions
 */
class BaseException extends Exception
{
    public ?string $key = null;

    public function __construct($message = "")
    {
        parent::__construct(empty($message) ? ($this->message ?? '') : '', 400);
    }

    public function getMessageBag(): MessageBag
    {
        $bag     = new MessageBag();
        $key     = $this->key ?? 'generic';
        $message = empty($this->message) ? 'Unexpected error occurred, please try again.' : $this->message;

        $bag->add($key, $message);
        return $bag;
    }
}
