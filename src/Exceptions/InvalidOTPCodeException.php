<?php

namespace YanGusik\TwoFactor\Exceptions;

use Throwable;

class InvalidOTPCodeException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The code has been expired or invalid.', 406, $previous);
    }
}