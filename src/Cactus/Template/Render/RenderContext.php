<?php


namespace Cactus\Template\Render;

use Cactus\I18n\I18nManager;
use Cactus\Routing\IRouteBuilder;
use Cactus\Util\AppConfiguration;
use Cactus\Util\ArrayPath;
use Cactus\Util\ClientRequest;

class RenderContext implements IRouteBuilder
{
    private IRouteBuilder $routeBuilder;
    private string $lang;
    private array $params;

    /**
     * RenderContext constructor.
     * @param IRouteBuilder $routeBuilder
     * @param array $i18n
     * @param array $params
     */
    public function __construct(IRouteBuilder $routeBuilder, string $lang, array $params = [])
    {
        $this->routeBuilder = $routeBuilder;
        $this->lang = $lang;
        $this->params = $params;
    }

    /**
     * @inheritDoc
     */
    public function buildUrl(string $method, string $routeName, array $parameters = []): string
    {
        $config = AppConfiguration::Instance();
        $request = ClientRequest::Instance();

        $format = $config->get("url.format");
        $lang = $request->getLang();
        $path = $this->routeBuilder->buildUrl($method, $routeName, $parameters);
        return sprintf($format, $lang, $path);
    }

    public function hasParam(string $key): bool
    {
        return ArrayPath::has($this->params, $key);
    }

    public function param(string $key): string
    {
        return ArrayPath::get($this->params, $key);
    }

    public function append(array $other): void
    {
        $this->params = array_replace_recursive($this->params, $other);
    }

    public function translate(string $key): string
    {
        $i18nManager = I18nManager::Instance();
        return $i18nManager->translate($this->lang, $key);
    }
}