<?php


namespace Cactus\Routing;


use Cactus\Routing\Exception\RouteNotFoundException;

interface IRouteBuilder
{
    /**
     * @param string $method
     * @param string $routeName
     * @param array $parameters
     * @return string
     * @throws RouteNotFoundException
     */
    public function buildUrl(string $method, string $routeName, array $parameters): string;
}