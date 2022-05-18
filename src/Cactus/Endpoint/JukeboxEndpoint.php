<?php

namespace Cactus\Endpoint;

use Banana\AppContext;
use Banana\Http\HttpCode;
use Banana\IO\FileException;
use Banana\Routing\IRouteEndpoint;
use Banana\Routing\Route;
use Banana\Routing\RouteException;
use Banana\Serialization\CsvException;
use Cactus\EasterEgg\Jukebox;
use JsonException;

class JukeboxEndpoint implements IRouteEndpoint
{
    /**
     * @param AppContext $context
     * @inheritDoc
     * @throws FileException
     * @throws CsvException
     */
    public function handle(AppContext $context, Route $route, array $parameters): string
    {
        $action = $parameters["action"];
        $song = null;
        switch ($action) {
            case "play":
                $song = Jukebox::play();
                break;
            case "stop":
                Jukebox::stop();
                break;
            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }

        http_response_code(HttpCode::SUCCESS_OK);
        header('Content-Type: application/json');
        return json_encode([
            "song" => $song
        ]);
    }
}