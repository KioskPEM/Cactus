<?php


namespace Cactus\User;


use Banana\AppContext;
use Exception;
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

    public function printTicket(AppContext $context, Printer $printer)
    {
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $this->appendImage($printer, "school", 1);

        $printer->setTextSize(3, 3);
        $this->append($context, $printer, "ticket.school_type", 2);
        $printer->setTextSize(2, 2);
        $this->append($context, $printer, "ticket.school_name", 2);

        $printer->setTextSize(1, 1);
        $this->append($context, $printer, "ticket.school_address", 1);
        $this->append($context, $printer, "ticket.school_phone", 3);

        $firstName = $this->user->getFirstName();
        $lastName = $this->user->getLastName();
        $uniqueId = $this->user->getUniqueId();

        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->append($context, $printer, "ticket.hello", 2, [
            $firstName,
            $lastName
        ]);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->append($context, $printer, "ticket.welcome", 1);
        $this->append($context, $printer, "ticket.school_name", 2);
        $this->append($context, $printer, "ticket.where_are_you", 3);

        $printer->setTextSize(2, 2);
        $this->append($context, $printer, "ticket.school_section", 3);

        $printer->setTextSize(1, 1);
        $this->append($context, $printer, "ticket.school_section_option_1", 1);
        $this->append($context, $printer, "ticket.school_section_option_2", 3);

        $barCodeContent = "USER-" . $uniqueId;
        $printer->setTextSize(1, 1);
        $printer->barcode($barCodeContent, Printer::BARCODE_CODE39);

        $printer->feed(2);
        $printer->setTextSize(2, 2);
        $this->appendImage($printer, "ok_hand", 1);
        $this->append($context, $printer, "ticket.have_fun", 2);
    }

    function append(AppContext $context, Printer $printer, string $key, int $feed, array $params = [])
    {
        $text = $context->translate($key);
        $text = vsprintf($text, $params);
        $printer->text($text);

        $printer->feed($feed);
    }

    function appendImage(Printer $printer, string $name, int $feed)
    {
        try {
            $logo = EscposImage::load(ASSET_PATH . "img" . DIRECTORY_SEPARATOR . $name . ".png");
            $printer->bitImage($logo);
        } catch (Exception $e) {
            $printer->text("Unable to print image");
        }

        $printer->feed($feed);
    }
}