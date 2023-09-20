<?php

namespace App\LogicLive\Common\Exceptions;

use Exception;
use Throwable;

class HttpClientException extends Exception
{
    public function __construct(string $message = '', Throwable | null $previous = null)
    {
        $code = 500;
        parent::__construct($message, $code, $previous);
    }
}
