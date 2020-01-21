<?php

namespace Cactus\Endpoint;


use Cactus\Http\HttpCode;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Cactus\Util\AppConfiguration;
use Exception;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

class PrintTicketEndpoint implements IRouteEndpoint
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function handle(Route $route, array $parameters): string
    {
        $port = AppConfiguration::get("printer.port");
        $connector = new FilePrintConnector($port);
        $printer = new Printer($connector);
        $printer->initialize();

        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->setTextSize(4, 4);
        $printer->text("Lycée");
        $printer->feed();
        $printer->setTextSize(2, 2);
        $printer->text("Pierre Emile Martin");

        $printer->feed(4);

        $firstName = $parameters["first_name"] ?? "John";
        $lastName = $parameters["last_name"] ?? "Doe";

        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Bienvenue, ");
        $printer->text($firstName);
        $printer->text(" ");
        $printer->text($lastName);
        $printer->feed(2);

        $id = $parameters["id"] ?? "0123456789";
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->barcode($id, Printer::BARCODE_CODE39);

        $printer->cut(Printer::CUT_PARTIAL);
        $printer->close();

        http_response_code(HttpCode::SUCCESS_NO_CONTENT);
        return "";
    }
}