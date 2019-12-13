<?php

namespace Cactus\Template\Pass;


use Cactus\Template\Render\RenderContext;
use Cactus\Template\TemplateManager;
use Cactus\Template\Template;

class EchoPass implements IRenderPass
{
    const PATTERN = '/\$\{' . TemplateManager::IDENTIFIER_PATTERN . '}/';

    /**
     * @param string $name
     * @param RenderContext $context
     * @param Template $template
     * @param string $subject
     * @return string
     */
    function execute(string $name, RenderContext $context, Template $template, string $subject): string
    {
        return preg_replace_callback(static::PATTERN, function ($matches) use ($context) {
            return $context->param($matches[1]);
        }, $subject);
    }
}