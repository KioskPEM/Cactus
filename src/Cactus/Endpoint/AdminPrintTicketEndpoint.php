<?php


namespace Cactus\Endpoint;


use Cactus\Http\HttpCode;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Cactus\Util\AppConfiguration;
use Cactus\Util\UrlBuilder;
use Mike42\Escpos\Printer;

class AdminPrintTicketEndpoint implements IRouteEndpoint
{
    private const HEADER_LINE = "Maintenance";
    private const ADMIN_ACCESS_LINE = "Accès administration";
    private const UPDATE_LINE = "Mise à jour forcée";
    private const EASTER_EGG_ACCESS_LINE = "Accès ...";

    private const GO_TO_ADMIN_PASS = "SEND-NUDES";
    private const GO_TO_UPDATE_PASS = "FORCE-UPDATE";
    private const GO_TO_EASTER_EGG_PASS = "EASTER-EGG";

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

        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $this->addCode($printer, self::ADMIN_ACCESS_LINE, self::GO_TO_ADMIN_PASS);
        $this->addCode($printer, self::UPDATE_LINE, self::GO_TO_UPDATE_PASS);
        $this->addCode($printer, self::EASTER_EGG_ACCESS_LINE, self::GO_TO_EASTER_EGG_PASS);

        $printer->cut(Printer::CUT_PARTIAL);
        $printer->close();

        $router = $route->getRouter();
        $urlBuilder = UrlBuilder::Instance();
        $adminPage = $urlBuilder->build($router, "admin.index", $parameters);
        header("Location: " . $adminPage, true, HttpCode::REDIRECT_SEE_OTHER);
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