<?php

namespace Cactus\Template\Render\Pass;

use Cactus\Template\Render\RenderContext;
use Cactus\Template\Template;
use Cactus\Template\TemplateManager;
use Cactus\Util\StringUtil;

class I18nPass implements IRenderPass
{
    const PATTERN = '/%\{' . TemplateManager::IDENTIFIER_PATTERN . '}/';

    /**
     * @inheritDoc
     */
    function execute(string $name, RenderContext $context, Template $template, string $subject): string
    {
        return preg_replace_callback(static::PATTERN, function ($matches) use ($name, $context) {
            $key = $matches[1];
            if (StringUtil::startWith($key, "page."))
                $key = "pages." . $name . substr($key, 4);

            return $context->translate($key);
        }, $subject);
    }
}