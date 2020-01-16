<?php


namespace Cactus\Template\Render\Pass;


use Cactus\Routing\Router;
use Cactus\Template\Render\RenderContext;
use Cactus\Template\Template;
use Cactus\Template\TemplateManager;
use Cactus\Util\ClientRequest;

class UrlPass implements IRenderPass
{
    const PATTERN = '/@\{' . TemplateManager::IDENTIFIER_PATTERN . '(\|((' . self::PARAM_ENTRY_PATTERN . ',)*' . self::PARAM_ENTRY_PATTERN . '))?}/';

    private const PARAM_ENTRY_PATTERN = TemplateManager::IDENTIFIER_PATTERN . "=([\w\d\\.\\-_]+)";
    private const PARAM_PATTERN = '/' . self::PARAM_ENTRY_PATTERN . '/';

    private Router $router;
    private string $format;

    public function __construct(Router $router, string $format)
    {
        $this->router = $router;
        $this->format = $format;
    }

    /**
     * @inheritDoc
     */
    function execute(string $name, RenderContext $context, Template $template, string $subject): string
    {
        return preg_replace_callback(static::PATTERN, function ($matches) use ($context) {
            $request = ClientRequest::Instance();
            $lang = $request->getLang();

            $parameters = [];
            if (array_key_exists(3, $matches)) {
                preg_match_all(self::PARAM_PATTERN, $matches[3], $results, PREG_SET_ORDER);
                foreach ($results as $entry)
                    $parameters[$entry[1]] = $entry[2];
            }

            $url = $this->router->generateUrl('GET', $matches[1], $parameters);
            return sprintf($this->format, $lang, $url);
        }, $subject);
    }
}