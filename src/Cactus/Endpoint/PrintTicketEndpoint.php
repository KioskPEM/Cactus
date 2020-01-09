<?php

namespace Cactus\Endpoint;


use Cactus\Template\Render\Pass\IRenderPass;
use Cactus\Template\Render\RenderContext;
use Cactus\Template\Template;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class PrintTicketEndpoint implements IRenderPass
{

    /**
     * @inheritDoc
     */
    function execute(string $name, RenderContext $context, Template $template, string $subject): string
    {
        $connector = new WindowsPrintConnector($com);
        $printer = new Printer($connector);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($text);
        $printer->feed();

        /*if ($qrCode === "true")
            $printer->qrCode($code, Printer::QR_ECLEVEL_L, 10);
        else*/
        $printer->barcode($code);

        $printer->feed();
        $printer->cut();
        $printer->close();

        return "OK";
    }
}