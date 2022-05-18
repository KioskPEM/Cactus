<?php

namespace Banana\Template\Render\Pass;

use Banana\Template\Render\RenderContext;
use Banana\Template\Template;
use Banana\Template\TemplateManager;

class LocalizePass implements IRenderPass
{
    const PATTERN = '/%\{' . TemplateManager::IDENTIFIER_PATTERN . '}/';

    /**
     * @inheritDoc
     */
    function execute(string $name, RenderContext $context, Template $template, string $subject): string
    {
        return preg_replace_callback(static::PATTERN, function ($matches) use ($name, $context) {
            $key = $matches[1];
            if (strpos($key, "page.") === 0)
                $key = "pages." . $name . substr($key, 4);

            $appContext = $context->getContext();
            return $appContext->translate($key);
        }, $subject);
    }
}