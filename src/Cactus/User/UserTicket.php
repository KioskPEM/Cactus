<?php


namespace Cactus\User;


use Mike42\Escpos\Printer;

class UserTicket
{
    private const HEADER_LINE_1 = "Lycée";
    private const HEADER_LINE_2 = "Pierre Emile Martin";
    private const WELCOME_LINE = "Bienvenue %s %s, ";

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
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->setTextSize(4, 4);
        $printer->text(self::HEADER_LINE_1);
        $printer->feed();
        $printer->setTextSize(2, 2);
        $printer->text(self::HEADER_LINE_2);

        $printer->feed(4);

        $firstName = $this->user->getFirstName();
        $lastName = $this->user->getLastName();
        $uniqueId = $this->user->getUniqueId();

        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $welcomeText = sprintf(self::WELCOME_LINE, $firstName, $lastName);
        $printer->text($welcomeText);
        $printer->feed(2);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $barCodeContent = "USER" . strval($uniqueId);
        $printer->barcode($barCodeContent, Printer::BARCODE_CODE39);
    }
}