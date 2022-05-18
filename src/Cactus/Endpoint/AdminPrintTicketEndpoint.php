<?php


namespace Cactus\Endpoint;


use Banana\AppContext;
use Banana\Http\HttpCode;
use Banana\Routing\IRouteEndpoint;
use Banana\Routing\Route;
use Mike42\Escpos\Printer;

class AdminPrintTicketEndpoint implements IRouteEndpoint
{
    private const HEADER_LINE = "Maintenance";
    private const ADMIN_ACCESS_LINE = "Administration";
    private const EASTER_EGG_ACCESS_LINE = "Games";

    private const GO_TO_ADMIN_PASS = "SENDNUDES";
    private const GO_TO_EASTER_EGG_PASS = "EASTEREGG";

    /**
     * @param AppContext $context
     * @inheritDoc
     */
    public function handle(AppContext $context, Route $route, array $parameters): string
    {
        $connectorClass = $context->config("printer.connector");
        $port = $context->config("printer.port");
        $connector = new $connectorClass($port);
        $printer = new Printer($connector);
        $printer->initialize();

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(2, 2);
        $printer->text(self::HEADER_LINE);

        $printer->setTextSize(1, 1);
        $this->addCode($printer, self::ADMIN_ACCESS_LINE, self::GO_TO_ADMIN_PASS);
        $this->addCode($printer, self::EASTER_EGG_ACCESS_LINE, self::GO_TO_EASTER_EGG_PASS);

        $printer->cut(Printer::CUT_PARTIAL);
        $printer->close();

        $router = $route->getRouter();
        $url = $router->buildUrl("GET", "admin.index");
        header("Location: " . $url, true, HttpCode::REDIRECT_SEE_OTHER);
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