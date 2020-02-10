<?php


namespace Cactus\Endpoint\User;


use Cactus\Http\HttpCode;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Cactus\Util\UrlBuilder;

class LoginEndpoint implements IRouteEndpoint
{
    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $id = intval($_POST["id"]);

        $router = $route->getRouter();
        $urlBuilder = UrlBuilder::Instance();
        $url = $urlBuilder->build($router, "user.profile", [
            "id" => $id
        ]);
        header("Location: " . $url, true, HttpCode::REDIRECT_SEE_OTHER);
        return "";
    }
}