<?php


namespace Banana\Template\Render\Pass;


use Banana\Template\Render\RenderContext;
use Banana\Template\Template;
use Banana\Template\TemplateManager;
use Banana\Template\TemplateRenderException;

class HandlerPass implements IRenderPass
{
    const PATTERN = '/#\{' . TemplateManager::IDENTIFIER_PATTERN . '}/';

    /**
     * @inheritDoc
     * @throws TemplateRenderException
     */
    function execute(string $name, RenderContext $context, Template $template, string $subject): string
    {
        return preg_replace_callback(static::PATTERN, function ($matches) use ($context, $template) {
            $methodName = $matches[1];
            $controller = $template->getController();

            if (!method_exists($controller, $methodName))
                throw new TemplateRenderException($template->getName(), "Missing handler " . $methodName);

            return $controller->$methodName($context);
        }, $subject);
    }
}