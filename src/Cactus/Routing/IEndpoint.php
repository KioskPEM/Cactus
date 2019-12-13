<?php


namespace Cactus\Routing;


use Cactus\Routing\Exception\RouteException;

interface IEndpoint
{
    /**
     * @param Route $route
     * @param array $parameters
     * @return string
     * @throws RouteException
     */
    public function handle(Route $route, array $parameters): string;
}