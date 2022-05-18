<?php

namespace Banana\Routing;


use Throwable;

class RouteNotFoundException extends RouteException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}