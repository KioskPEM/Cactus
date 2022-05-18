<?php

namespace Banana\Template;


use Throwable;

class TemplateNotFoundException extends TemplateException
{
    public function __construct(string $template, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($template, $message, $code, $previous);
    }
}