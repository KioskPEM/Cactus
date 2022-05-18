<?php

namespace Banana\Serialization;

use Exception;
use Throwable;

class CsvException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}