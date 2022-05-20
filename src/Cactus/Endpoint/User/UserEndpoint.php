<?php


namespace Cactus\Endpoint\User;


use Banana\AppContext;
use Banana\Http\HttpCode;
use Banana\Routing\IRouteEndpoint;
use Banana\Routing\Route;
use Banana\Routing\RouteException;
use Cactus\User\Exception\UserException;
use Cactus\User\UserManager;

class UserEndpoint implements IRouteEndpoint
{
    /**
     * @param AppContext $context
     * @inheritDoc
     * @throws UserException
     */
    public function handle(AppContext $context, Route $route, array $parameters): string
    {
        $action = $parameters["action"];
        $id = intval($parameters["user_id"]);

        $userManager = $context->getService("Cactus\User\UserManager");
        $user = $userManager->loginUser($id);

        switch ($action) {
            case "set_internship":
                $user->askForInternship();
                break;
            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }

        $userManager->updateUser($user);

        $router = $route->getRouter();
        $url = $router->buildUrl("GET", "internship.success", null);
        header("Location: " . $url, true, HttpCode::REDIRECT_SEE_OTHER);
        return "";
    }
}