<?php

namespace Cactus\Template\Render\Pass;


use Cactus\Template\Render\RenderContext;
use Cactus\Template\Template;
use Cactus\Template\TemplateManager;

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