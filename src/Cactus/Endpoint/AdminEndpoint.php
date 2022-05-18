<?php

namespace Cactus\Endpoint;

use Banana\AppContext;
use Banana\Http\HttpCode;
use Banana\Routing\IRouteEndpoint;
use Banana\Routing\Route;
use Banana\Routing\RouteException;
use Banana\Routing\RouteFormatException;
use Banana\Routing\RouteNotFoundException;

class AdminEndpoint implements IRouteEndpoint
{
    /**
     * @param AppContext $context
     * @param Route $route
     * @param array $parameters
     * @return string
     * @throws RouteException
     * @throws RouteFormatException
     * @throws RouteNotFoundException
     * @inheritDoc
     */
    public function handle(AppContext $context, Route $route, array $parameters): string
    {
        $action = $parameters["action"];

        switch ($action) {
            case "set_register_mode":
                $context->setConfig("home-page", "register.index");
                break;
            case "set_slideshow_mode":
                $context->setConfig("home-page", "slideshow.index");
                break;
            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }

        //TODO: Save config

        $router = $route->getRouter();
        $url = $router->buildUrl("GET", "admin.index");
        header("Location: " . $url, true, HttpCode::REDIRECT_SEE_OTHER);
        return "";
    }
}