<?php

namespace Cactus\Endpoint;

use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

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
            case "info":
                phpinfo();
                return "";
            case "print":

                $text = $_GET['text'] ?? "";
                $com = $_GET['com'] ?? "COM1";
                $connector = new WindowsPrintConnector($com);
                $printer = new Printer($connector);
                $printer->text($text);
                $printer->barcode("azerty", Printer::BARCODE_CODE39);
                $printer->cut();
                $printer->close();
                return "PRINTED_OR_IS_IT?";

            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }
    }
}