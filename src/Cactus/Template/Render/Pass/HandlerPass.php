<?php


namespace Cactus\Template\Render\Pass;


use Cactus\Template\Exception\TemplateRenderException;
use Cactus\Template\Render\RenderContext;
use Cactus\Template\Template;
use Cactus\Template\TemplateManager;

class HandlerPass implements IRenderPass
{
    const PATTERN = '/#\{' . TemplateManager::IDENTIFIER_PATTERN . '}/';

    /**
     * @inheritDoc
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