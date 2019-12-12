<?php

namespace Cactus\Routing\Exception;


use Cactus\Http\Exception\HttpException;
use Throwable;

class RouteException extends HttpException
{

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}