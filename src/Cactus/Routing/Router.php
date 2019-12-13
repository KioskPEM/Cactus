<?php

namespace Cactus\Routing;

use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteNotFoundException;

class Router
{
    private array $routes = [];

    public function hasMethod(string $method): bool
    {
        return array_key_exists($method, $this->routes);
    }

    public function get(string $name, string $path, IRouteEndpoint $endpoint): Route
    {
        return $this->register($name, "GET", $path, $endpoint);
    }

    public function post(string $name, string $path, IRouteEndpoint $endpoint): Route
    {
        return $this->register($name, "POST", $path, $endpoint);
    }

    private function register(string $name, string $method, string $path, IRouteEndpoint $endpoint): Route
    {
        $route = Route::parse($name, trim($path, '/'), $endpoint);
        $this->routes[$method][$name] = $route;
        return $route;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $matches
     * @return Route
     * @throws RouteNotFoundException
     */
    public function resolveRoute(string $url, string $method, array &$matches): Route
    {
        if (!$this->hasMethod($method))
            return null;

        $methodRoutes = $this->routes[$method];
        foreach ($methodRoutes as $route) {
            /* @var $route Route */
            if ($route->match($url, $matches))
                return $route;
        }

        throw new RouteNotFoundException("No route found (url: " . $url . ')', HttpCode::CLIENT_NOT_FOUND);
    }

}
