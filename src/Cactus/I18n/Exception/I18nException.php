<?php

namespace Cactus\I18n\Exception;

use Exception;
use Throwable;

class I18nException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}