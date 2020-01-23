<?php

namespace Cactus\Routing;

use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteFormatException;
use Cactus\Routing\Exception\RouteNotFoundException;

class Router implements IRouteBuilder
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

    public function register(string $name, string $method, string $path, IRouteEndpoint $endpoint): Route
    {
        $route = Route::parse($this, $name, trim($path, '/'), $endpoint);
        $this->routes[$method][$name] = $route;
        return $route;
    }

    /**
     * @param string $path
     * @param string $method
     * @param array $matches
     * @return Route
     * @throws RouteNotFoundException
     */
    public function resolveRoute(string $path, string $method, array &$matches): Route
    {
        if (!$this->hasMethod($method))
            return null;

        $methodRoutes = $this->routes[$method];
        foreach ($methodRoutes as $route) {
            /* @var $route Route */
            if ($route->match($path, $matches))
                return $route;
        }

        throw new RouteNotFoundException("No route found (url: " . $path . ')', HttpCode::CLIENT_NOT_FOUND);
    }

    /**
     * @param string $method
     * @param string $routeName
     * @param array $parameters
     * @return string
     * @throws RouteNotFoundException
     */
    public function buildUrl(string $method, string $routeName, array $parameters = []): string
    {
        if ($method === '*') {
            $methods = ["GET", "POST"];
            foreach ($methods as $method) {
                try {
                    return $this->buildUrl($method, $routeName, $parameters);
                } catch (RouteNotFoundException $e) {
                    continue;
                }
            }

            throw new RouteNotFoundException("Not found (" . $routeName . ')', HttpCode::CLIENT_NOT_FOUND);
        }

        if (!$this->hasMethod($method))
            throw new RouteNotFoundException("Method not allowed", HttpCode::CLIENT_METHOD_NOT_ALLOWED);

        $routes = $this->routes[$method];
        if (!array_key_exists($routeName, $routes))
            throw new RouteNotFoundException("Not found (" . $routeName . ')', HttpCode::CLIENT_NOT_FOUND);

        /* @var $route Route */
        $route = $routes[$routeName];
        $routePath = $route->getPath();

        return preg_replace_callback(Route::PARAM_PATTERN, function ($matches) use ($parameters) {
            $paramName = $matches[1];
            if (array_key_exists($paramName, $parameters))
                return $parameters[$paramName];
            else
                throw new RouteFormatException("Missing parameter \"" . $paramName . '"');
        }, $routePath);
    }

}
