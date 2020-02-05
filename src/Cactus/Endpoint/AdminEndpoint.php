<?php

namespace Cactus\Endpoint;

use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Cactus\Util\UrlBuilder;

class AdminEndpoint implements IRouteEndpoint
{
    private const EXIT_CMD = "kill ";
    private const START_CMD = "startx";

    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $action = $parameters["action"];

        switch ($action) {
            case "quit":
                shell_exec(self::EXIT_CMD);
                break;
            case "restart";
                shell_exec(self::EXIT_CMD);
                shell_exec(self::START_CMD);
                break;
            case "clear_cache":
                session_destroy();
                break;
            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }

        $router = $route->getRouter();
        $urlBuilder = UrlBuilder::Instance();
        $adminPage = $urlBuilder->build($router, "admin.index", $parameters);
        header("Location: " . $adminPage, true, HttpCode::REDIRECT_SEE_OTHER);
        return "";
    }
}