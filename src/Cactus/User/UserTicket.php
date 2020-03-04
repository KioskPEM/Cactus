<?php


namespace Cactus\User;


use Cactus\I18n\I18nManager;
use Cactus\Util\ClientRequest;
use Mike42\Escpos\Printer;

class UserTicket
{
    private User $user;

    /**
     * UserTicket constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }


    public function printTicket(Printer $printer)
    {
        $i18nManager = I18nManager::Instance();
        $clientRequest = ClientRequest::Instance();
        $lang = $clientRequest->getLang();

        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->setTextSize(4, 4);
        $headerLine1 = $i18nManager->translate($lang, "ticket.header_1");
        $printer->text($headerLine1);
        $printer->feed();
        $printer->setTextSize(2, 2);
        $headerLine2 = $i18nManager->translate($lang, "ticket.header_2");
        $printer->text($headerLine2);

        $printer->feed(4);

        $firstName = $this->user->getFirstName();
        $lastName = $this->user->getLastName();
        $uniqueId = $this->user->getUniqueId();

        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $welcomeText = $i18nManager->translate($lang, "ticket.welcome");
        $welcomeText = sprintf($welcomeText, $firstName, $lastName);
        $printer->text($welcomeText);
        $printer->feed(2);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $barCodeContent = "USER-" . $uniqueId;
        $printer->barcode($barCodeContent, Printer::BARCODE_CODE39);
    }
}