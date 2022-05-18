<?php


namespace Banana\Routing;


use Banana\AppContext;

interface IRouteEndpoint
{
    /**
     * @param AppContext $context
     * @param Route $route
     * @param array $parameters
     * @return string
     * @throws RouteException
     */
    public function handle(AppContext $context, Route $route, array $parameters): string;
}