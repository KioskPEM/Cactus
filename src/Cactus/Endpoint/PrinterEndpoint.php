<?php


namespace Cactus\Endpoint;


use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Cactus\Util\AppConfiguration;
use Exception;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

class PrinterEndpoint implements IRouteEndpoint
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function handle(Route $route, array $parameters): string
    {
        $config = AppConfiguration::Instance();

        $connectorClass = $config->get("printer.connector");
        $port = $config->get("printer.port");
        $connector = new $connectorClass($port);
        $printer = new Printer($connector);

        $printer->initialize();

        switch ($parameters["action"]) {
            case "cut":
                $printer->cut();
                $printer->close();
                break;
            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }

        http_response_code(HttpCode::SUCCESS_NO_CONTENT);
        return "";
    }
}