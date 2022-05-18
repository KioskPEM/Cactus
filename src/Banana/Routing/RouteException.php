<?php

namespace Banana\Routing;


use Banana\Http\HttpException;
use Throwable;

class RouteException extends HttpException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}