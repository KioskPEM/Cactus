<?php


namespace Cactus\Routing\Exception;


use Throwable;

class RouteFormatException extends RouteException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}