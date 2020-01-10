<?php

namespace Cactus\Endpoint;


use Cactus\Http\HttpCode;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class PrintTicketEndpoint implements IRouteEndpoint
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

        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->setTextSize(4, 4);
        $printer->text("Lycée");
        $printer->feed();
        $printer->setTextSize(2, 2);
        $printer->text("Pierre Emile Martin");

        $printer->feed(2);

        $firstName = $parameters["first_name"] ?? "John";
        $lastName = $parameters["last_name"] ?? "Doe";

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setTextSize(1, 1);
        $printer->text("Bienvenue, ");
        $printer->text($firstName);
        $printer->text(" ");
        $printer->text($lastName);
        $printer->feed();

        $id = $parameters["id"] ?? "0123456789";
        $printer->barcode($id, Printer::BARCODE_CODE39);

        $printer->cut(Printer::CUT_PARTIAL);
        $printer->close();

        http_response_code(HttpCode::SUCCESS_NO_CONTENT);
        return "";
    }
}