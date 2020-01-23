<?php


namespace Cactus\Template\Render;

use Cactus\Routing\IRouteBuilder;
use Cactus\Util\AppConfiguration;
use Cactus\Util\ArrayPath;
use Cactus\Util\ClientRequest;

class RenderContext implements IRouteBuilder
{
    private IRouteBuilder $routeBuilder;
    private array $i18n;
    private array $params;

    /**
     * RenderContext constructor.
     * @param IRouteBuilder $routeBuilder
     * @param array $i18n
     * @param array $params
     */
    public function __construct(IRouteBuilder $routeBuilder, array $i18n, array $params = [])
    {
        $this->routeBuilder = $routeBuilder;
        $this->i18n = $i18n;
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
        $value = ArrayPath::get($this->i18n, $key);

        if (!$value)
            return $key;

        if (is_array($value)) {
            $temp = '';
            foreach ($value as $entry)
                $temp .= $entry . '<br>';
            $value = $temp;
        }

        return $value;
    }
}