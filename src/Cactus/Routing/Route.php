<?php

namespace Cactus\Routing;


use Cactus\Routing\Exception\RouteException;

class Route
{
    const PARAM_PATTERN = "/:([a-z_]+)\{([^\/]+)\}/";

    private Router $router;
    private string $name;
    private string $path;
    private string $regex;
    private IRouteEndpoint $endpoint;

    private function __construct(Router $router, string $name, string $path, string $regex, IRouteEndpoint $endpoint)
    {
        $this->router = $router;
        $this->name = $name;
        $this->path = $path;
        $this->regex = $regex;
        $this->endpoint = $endpoint;
    }

    public function match(string $path, array &$matches)
    {
        return preg_match($this->regex, $path, $matches);
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    public static function parse(Router $router, string $name, string $path, IRouteEndpoint $handler): Route
    {
        return new Route(
            $router,
            $name,
            $path,
            "#^" . preg_replace(Route::PARAM_PATTERN, "(?P<\$1>\$2)", $path) . "\$#",
            $handler
        );
    }
}