<?php

namespace Cactus\Endpoint;

use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Cactus\Util\AppConfiguration;

class AdminEndpoint implements IRouteEndpoint
{
    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $action = $parameters["action"];

        switch ($action) {
            case "update":
                $updateUrl = AppConfiguration::get("url.root") . "/update.php";
                return "<a href=\"$updateUrl\" onclick=\"this.text = 'Cactus is now updating...'\">Click here to update Cactus.</a>";
            case "quit":
                return system("cmd /c C:\Cactus\stop.bat");
            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }
    }
}