<?php

namespace Cactus\Endpoint;

use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;

class AdminEndpoint implements IRouteEndpoint
{
    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $action = $parameters["action"];

        switch ($action) {
            case "clear_cache":
                session_destroy();
                break;
            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }

        http_response_code(HttpCode::SUCCESS_NO_CONTENT);
        return "";
    }
}