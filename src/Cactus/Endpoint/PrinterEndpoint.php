<?php


namespace Cactus\Endpoint;


use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class PrinterEndpoint implements IRouteEndpoint
{
    private string $port;

    /**
     * PrintTicketEndpoint constructor.
     * @param string $port
     */
    public function __construct(string $port)
    {
        $this->port = $port;
    }

    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $connector = new WindowsPrintConnector($this->port);
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