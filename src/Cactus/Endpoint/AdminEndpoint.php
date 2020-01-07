<?php

namespace Cactus\Endpoint;

use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;

class AdminEndpoint implements IRouteEndpoint
{
    private string $updateUrl;

    /**
     * AdminEndpoint constructor.
     * @param string $updateUrl
     */
    public function __construct(string $updateUrl)
    {
        $this->updateUrl = $updateUrl;
    }

    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $action = $parameters["action"];

        switch ($action) {
            case "update":
                return "<a href=\"$this->updateUrl\" onclick=\"this.text = 'Cactus is now updating...'\">Click here to update Cactus.</a>";
            case "quit":
                return system("cmd /c C:\Cactus\stop.bat");
            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }
    }
}