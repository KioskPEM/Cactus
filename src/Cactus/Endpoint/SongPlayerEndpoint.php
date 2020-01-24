<?php

namespace Cactus\Endpoint;

use Cactus\EasterEgg\SongPlayer;
use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;

class SongPlayerEndpoint implements IRouteEndpoint
{
    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $songPlayer = SongPlayer::Instance();

        $action = $parameters["action"];
        switch ($action) {
            case "play":
                $song = $parameters["song"];
                if ($song === "*")
                    $songPlayer->playRandom();
                else
                    $songPlayer->playAt($song);
                break;
            case "stop":
                $songPlayer->stop();
                break;
            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }

        http_response_code(HttpCode::SUCCESS_OK);
        header('Content-Type: application/json');
        echo json_encode([
            "song" => $songPlayer->getCurrentSong()
        ]);
    }
}