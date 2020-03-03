<?php

namespace Cactus\Endpoint;

use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Cactus\Util\AppConfiguration;
use Cactus\Util\UrlBuilder;

class AdminEndpoint implements IRouteEndpoint
{
    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $action = $parameters["action"];

        switch ($action) {
            case "set_presentation_mode":
                $config = AppConfiguration::Instance();
                $config->set("home-page", "presentation.index");
                $config->save();
                break;
            case "set_sign_up_mode":
                $config = AppConfiguration::Instance();
                $config->set("home-page", "sign-up.index");
                $config->save();
                break;
            case "set_slideshow_mode":
                header("Location: https://drive.google.com/file/d/1_i9u8hEeFmCYvRE7fRR-QBDYYzZzrCGj/view?usp=sharing", true, HttpCode::REDIRECT_SEE_OTHER);
                return "";
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