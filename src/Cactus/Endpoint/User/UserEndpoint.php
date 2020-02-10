<?php


namespace Cactus\Endpoint;


use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Cactus\Util\UrlBuilder;

class UserEndpoint implements IRouteEndpoint
{
    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $action = $parameters["action"];
        $data = $parameters["data"];

        switch ($action) {
            case "":
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