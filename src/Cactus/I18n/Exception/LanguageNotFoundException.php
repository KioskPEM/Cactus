<?php

namespace Cactus\I18n\Exception;

use Throwable;

class LanguageNotFoundException extends I18nException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}