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

class JukeboxEndpoint implements IRouteEndpoint
{
    /**
     * @param AppContext $context
     * @inheritDoc
     */
    public function handle(AppContext $context, Route $route, array $parameters): string
    {
        $action = $parameters["action"];
        $song = null;
        switch ($action) {
            case "play":
                try {
                    $song = Jukebox::play();
                } catch (FileException|CsvException $e) {
                    return self::report(HttpCode::SERVER_ERROR, [
                        "error" => "Failed to initialize Jukebox"
                    ]);
                }
                break;
            case "stop":
                Jukebox::stop();
                break;
            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }

        return self::report(HttpCode::SUCCESS_OK, [
            "song" => $song
        ]);
    }

    private static function report(int $code, array $data)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        return json_encode($data);
    }
}