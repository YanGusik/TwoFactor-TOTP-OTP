<?php

namespace YanGusik\TwoFactor\Exceptions;

use Throwable;

class InvalidArgumentException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}