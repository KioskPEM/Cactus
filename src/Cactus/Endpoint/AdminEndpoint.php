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

                $text = $_GET['text'] ?? "test";
                $com = $_GET['com'] ?? "COM1";
                $code = $_GET['code'] ?? "POMME";
                $qrCode = $_GET['qrcode'] ?? "false";

                $connector = new WindowsPrintConnector($com);
                $printer = new Printer($connector);
                $printer->initialize();
                $printer->text($text);
                $printer->feed();

                if ($qrCode === "true")
                    $printer->qrCode($code);
                else
                    $printer->barcode($code);

                $printer->feed();
                $printer->cut();
                $printer->close();
                return "PRINTED_OR_IS_IT?";

            default:
                throw new RouteException("Invalid action", HttpCode::CLIENT_BAD_REQUEST);
        }
    }
}