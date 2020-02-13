<?php


namespace Cactus\Endpoint\User;


use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Cactus\User\Exception\UserException;
use Cactus\User\UserManager;
use Cactus\Util\UrlBuilder;

class UserEndpoint implements IRouteEndpoint
{
    /**
     * @inheritDoc
     * @throws UserException
     */
    public function handle(Route $route, array $parameters): string
    {
        $action = $parameters["action"];
        $id = intval($parameters["user_id"]);

        $userManager = UserManager::Instance();
        $user = $userManager->loginUser($id);

        switch ($action) {
            case "set_placement":
                $user->setPlacement(true);
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