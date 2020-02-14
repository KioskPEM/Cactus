<?php

namespace Cactus\Endpoint;

use Cactus\EasterEgg\Jukebox;
use Cactus\Exception\FileException;
use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;

class JukeboxEndpoint implements IRouteEndpoint
{
    /**
     * @inheritDoc
     * @throws FileException
     */
    public function handle(Route $route, array $parameters): string
    {
        $songPlayer = Jukebox::Instance();

        $action = $parameters["action"];
        switch ($action) {
            case "play":
                $songPlayer->playRandom();
                break;
            case "stop":
                $songPlayer->stop();
                break;
            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }

        http_response_code(HttpCode::SUCCESS_OK);
        header('Content-Type: application/json');
        return json_encode([
            "song" => $songPlayer->getCurrentSong()
        ]);
    }
}