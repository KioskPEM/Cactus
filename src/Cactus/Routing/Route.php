<?php

namespace Cactus\Routing;


use Cactus\Routing\Exception\RouteException;

class Route
{
    const PARAM_PATTERN = "#:([a-z]+)(?:\{(.+)\})?#";

    private string $name;
    private string $path;
    private string $regex;
    private IEndpoint $handler;

    public function __construct(string $name, string $path, string $regex, IEndpoint $handler)
    {
        $this->name = $name;
        $this->path = $path;
        $this->regex = $regex;
        $this->handler = $handler;
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
}