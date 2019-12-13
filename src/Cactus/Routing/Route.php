<?php

namespace Cactus\Routing;


use Cactus\Routing\Exception\RouteException;

class Route
{
    const PARAM_PATTERN = "#:([a-z]+)(?:\{(.+)\})?#";

    private string $name;
    private string $path;
    private string $regex;
    private IRouteEndpoint $endpoint;

    public function __construct(string $name, string $path, string $regex, IRouteEndpoint $endpoint)
    {
        $this->name = $name;
        $this->path = $path;
        $this->regex = $regex;
        $this->endpoint = $endpoint;
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

        return $this->endpoint->handle($this, $matches);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public static function parse(string $name, string $path, IRouteEndpoint $handler): Route
    {
        return new Route(
            $name,
            $path,
            '#^' . preg_replace(Route::PARAM_PATTERN, '(?P<$1>$2)', $path) . '$#',
            $handler
        );
    }
}