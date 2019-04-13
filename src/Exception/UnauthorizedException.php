<?php

namespace App\Exception;

class UnauthorizedException extends \RuntimeException
{
    /**
     * UnauthorizedException constructor.
     *
     * @param string $message
     */
    public function __construct($message = 'Unauthroized Access')
    {
        \Exception::__construct($message, 403);
    }
}
