<?php


namespace Banana\Template\Render\Pass;


use Banana\Routing\RouteFormatException;
use Banana\Routing\RouteNotFoundException;
use Banana\Template\Render\RenderContext;
use Banana\Template\Template;
use Banana\Template\TemplateManager;

class UrlPass implements IRenderPass
{
    const PATTERN = '/@\{' . TemplateManager::IDENTIFIER_PATTERN . '(\|((' . self::PARAM_ENTRY_PATTERN . ',)*' . self::PARAM_ENTRY_PATTERN . '))?}/';

    private const PARAM_ENTRY_PATTERN = TemplateManager::IDENTIFIER_PATTERN . "=([\w\d\\.\\-\\*_]+)";
    private const PARAM_PATTERN = '/' . self::PARAM_ENTRY_PATTERN . '/';

    /**
     * @inheritDoc
     */
    function execute(string $name, RenderContext $context, Template $template, string $subject): string
    {
        return preg_replace_callback(static::PATTERN, function ($matches) use ($context) {
            $parameters = [];
            if (array_key_exists(3, $matches)) {
                preg_match_all(self::PARAM_PATTERN, $matches[3], $results, PREG_SET_ORDER);
                foreach ($results as $entry)
                    $parameters[$entry[1]] = $entry[2];
            }

            try {
                $router = $context->getRouter();
                return $router->buildUrl('*', $matches[1], $parameters);
            } catch (RouteFormatException|RouteNotFoundException $e) {
                return $matches[1];
            }
        }, $subject);
    }
}