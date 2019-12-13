<?php

namespace Cactus\Template\Exception;

use Exception;
use Throwable;

class TemplateException extends Exception
{
    private string $template;

    public function __construct(string $template, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->template = $template;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }
}