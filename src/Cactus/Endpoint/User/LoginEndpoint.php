<?php


namespace Cactus\Endpoint\User;


use Banana\AppContext;
use Banana\Http\HttpCode;
use Banana\Routing\IRouteEndpoint;
use Banana\Routing\Route;

class LoginEndpoint implements IRouteEndpoint
{
    /**
     * @param AppContext $context
     * @inheritDoc
     */
    public function handle(AppContext $context, Route $route, array $parameters): string
    {
        $id = intval($_POST["id"]);

        $router = $route->getRouter();
        $url = $router->buildUrl("GET", "user.profile", [
            "user_id" => $id
        ]);
        header("Location: " . $url, true, HttpCode::REDIRECT_SEE_OTHER);
        return "";
    }
}