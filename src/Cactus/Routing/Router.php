<?php

namespace Cactus\Routing;

use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteNotFoundException;

class Router
{
    private $routes = [];

    public function get(string $name, string $path, IEndpoint $handler): Route
    {
        return $this->register($name, "GET", $path, $handler);
    }

    private function register(string $name, string $method, string $path, IEndpoint $handler): Route
    {
        $route = Route::parse($name, trim($path, '/'), $handler);
        $this->routes[$method][$name] = $route;
        return $route;
    }

    public function post(string $name, string $path, IEndpoint $handler): Route
    {
        return $this->register($name, "POST", $path, $handler);
    }

    /**
     * @param string $method
     * @param string $lang
     * @param string $routeName
     * @param array ...$parameters
     * @return string
     * @throws RouteNotFoundException
     */
    public function createUrl(string $method, string $lang, string $routeName, array ...$parameters)
    {
        if (!$this->hasMethod($method))
            throw new RouteNotFoundException("Unsupported method (" . $method . ')', HttpCode::CLIENT_METHOD_NOT_ALLOWED);

        $routes = $this->routes[$method];
        if (!array_key_exists($routeName, $routes))
            throw new RouteNotFoundException("Not found (" . $routeName . ')', HttpCode::CLIENT_NOT_FOUND);

        /* @var $route Route */
        $route = $routes[$routeName];
        return $route->buildUrl($lang, $parameters);
    }

    public function hasMethod(string $method): bool
    {
        return array_key_exists($method, $this->routes);
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

        throw new RouteNotFoundException("No route found (url: " . $url . ')', 404);
    }

}
