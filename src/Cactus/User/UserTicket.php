<?php


namespace Cactus\User;


use Cactus\I18n\I18nManager;
use Cactus\Util\ClientRequest;
use Mike42\Escpos\EscposImage;
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
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        try {
            $logo = EscposImage::load(ASSET_PATH . "img" . DIRECTORY_SEPARATOR . "school.png");
            $printer->bitImage($logo);
            $printer->feed();
        } catch (\Exception $e) {
            $printer->text("Unable to print image");
            $printer->feed();
        }

        $printer->setTextSize(3, 3);
        $this->append($printer, "ticket.school_type", 2);
        $this->append($printer, "ticket.school_name", 4);

        $firstName = $this->user->getFirstName();
        $lastName = $this->user->getLastName();
        $uniqueId = $this->user->getUniqueId();

        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->append($printer, "ticket.hello", 2, [
            $firstName,
            $lastName
        ]);
        $this->append($printer, "ticket.welcome", 2);

        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->setTextSize(2, 2);
        $this->append($printer, "ticket.school_section", 2);

        $printer->setTextSize(1, 1);
        $this->append($printer, "ticket.school_section_option", 2);

        $barCodeContent = "USER-" . $uniqueId;
        $printer->setTextSize(1, 1);
        $printer->barcode($barCodeContent, Printer::BARCODE_CODE39);

        $this->append($printer, "ticket.have_fun", 4);

    }

    function append(Printer $printer, string $key, int $feed = 2, array $params = [])
    {
        $i18nManager = I18nManager::Instance();
        $clientRequest = ClientRequest::Instance();
        $lang = $clientRequest->getLang();
        $text = sprintf($i18nManager->translate($lang, $key), $params);
        $printer->text($text);
        $printer->feed($feed);
    }
}