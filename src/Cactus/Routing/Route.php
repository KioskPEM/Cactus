<?php

namespace Cactus\Routing;


use Cactus\App;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\Exception\RouteFormatException;

class Route
{
    const PARAM_PATTERN = "#:([a-z]+)(?:\{(.+)\})?#";

    private $name;
    private $path;
    private $regex;
    private $handler;

    /**
     * Route constructor.
     * @param string $name
     * @param string $path
     * @param string $regex
     * @param IEndpoint $handler
     */
    public function __construct(string $name, string $path, string $regex, IEndpoint $handler)
    {
        $this->name = $name;
        $this->path = $path;
        $this->regex = $regex;
        $this->handler = $handler;
    }

    public static function parse(string $name, string $path, IEndpoint $handler): Route
    {
        return new Route(
            $name,
            $path,
            '#^' . preg_replace(Route::PARAM_PATTERN, '(?P<$1>$2)', $path) . '$#',
            $handler
        );
    }

    public function match(string $url, array &$matches)
    {
        return preg_match($this->regex, $url, $matches);
    }

    /**
     * @param array $matches
     * @return string
     * @throws RouteException
     */
    public function call(array $matches): string
    {
        // prevent security exploits
        foreach ($matches as $key => $value) {
            if (!is_string($key) || is_int($key))
                unset($matches[$key]);
        }

        return $this->handler->handle($this, $matches);
    }

    /**
     * Builds a valid url pointing to this route
     *
     * @param string $lang
     * @param array $parameters
     * @return string the url
     */
    public function buildUrl(string $lang, array $parameters): string
    {
        $path = preg_replace_callback(self::PARAM_PATTERN, function ($matches) use ($parameters) {
            $paramName = $matches[1];
            if (array_key_exists($paramName, $parameters))
                return $parameters[$matches[1]];
            else
                throw new RouteFormatException("Missing parameter \"" . $paramName . '"');
        }, $this->path);

        $app = App::Instance();
        $rootUrl = $app->config("url.root");
        return $rootUrl . '/' . $lang . '/' . $path;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}