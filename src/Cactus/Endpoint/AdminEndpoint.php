<?php

namespace Cactus\Endpoint;

use Banana\AppContext;
use Banana\Http\HttpCode;
use Banana\IO\FileException;
use Banana\Routing\IRouteEndpoint;
use Banana\Routing\Route;
use Banana\Routing\RouteException;
use Banana\Serialization\JsonSerializer;
use JsonException;

class AdminEndpoint implements IRouteEndpoint
{
    /**
     * @param AppContext $context
     * @param Route $route
     * @param array $parameters
     * @return string
     * @inheritDoc
     */
    public function handle(AppContext $context, Route $route, array $parameters): string
    {
        $action = $parameters["action"];

        switch ($action) {
            case "set_register_mode":
                $context->config("home-page", "register.index");
                break;
            case "set_slideshow_mode":
                $context->config("home-page", "slideshow.index");
                break;
            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }

        //TODO: Improve config saving
        try {
            $config = $context->getConfig();
            JsonSerializer::serializeFile(CONFIG_PATH, $config);
        } catch (FileException|JsonException $e) {
            throw new RouteException("Failed to save config");
        }

        $router = $route->getRouter();
        $url = $router->buildUrl("GET", "admin.index");
        header("Location: " . $url, true, HttpCode::REDIRECT_SEE_OTHER);
        return "";
    }
}