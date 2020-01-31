<?php


namespace Cactus\Endpoint;


use Cactus\Http\HttpCode;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Cactus\Util\AppConfiguration;
use Mike42\Escpos\Printer;

class AdminPrintTicketEndpoint implements IRouteEndpoint
{
    private const HEADER_LINE = "Maintenance";
    private const ADMIN_ACCESS_LINE = "Accès administration";
    private const UPDATE_LINE = "Mise à jour forcée";
    private const EASTER_EGG_ACCESS_LINE = "Accès ...";

    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $config = AppConfiguration::Instance();

        $connectorClass = $config->get("printer.connector");
        $port = $config->get("printer.port");
        $connector = new $connectorClass($port);
        $printer = new Printer($connector);
        $printer->initialize();

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(2, 2);
        $printer->text(self::HEADER_LINE);

        $printer->feed(4);
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $this->addCode($printer, self::ADMIN_ACCESS_LINE, "SENDNUDES");
        $this->addCode($printer, self::UPDATE_LINE, "FORCEUPDT");
        $this->addCode($printer, self::EASTER_EGG_ACCESS_LINE, "EQSTEREGG");

        $printer->cut(Printer::CUT_PARTIAL);
        $printer->close();

        http_response_code(HttpCode::SUCCESS_NO_CONTENT);
        return "";
    }

    private function addCode(Printer $printer, string $text, string $content)
    {
        $printer->feed(4);
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text($text);
        $printer->feed(2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->barcode($content, Printer::BARCODE_CODE39);
    }
}