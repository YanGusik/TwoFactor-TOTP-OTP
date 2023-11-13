<?php

namespace YanGusik\TwoFactor\Exceptions;

use Throwable;

class NotifiableNotFoundException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('Notifiable model is not specified in the config file.', $code, $previous);
    }
}