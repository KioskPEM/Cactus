<?php

namespace Banana\Template\Render\Pass;


use Banana\Template\Render\IRenderPass;
use Banana\Template\Render\RenderContext;
use Banana\Template\Template;
use Banana\Template\TemplateManager;

class EchoPass implements IRenderPass
{
    const PATTERN = '/\$\{' . TemplateManager::IDENTIFIER_PATTERN . '}/';

    /**
     * @inheritDoc
     */
    function execute(string $name, RenderContext $context, Template $template, string $subject): string
    {
        return preg_replace_callback(static::PATTERN, function ($matches) use ($context) {
            return $context->param($matches[1]);
        }, $subject);
    }
}