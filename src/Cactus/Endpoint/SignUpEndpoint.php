<?php

namespace Cactus\Endpoint;

use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Cactus\User\User;
use Cactus\User\UserTicket;
use Cactus\Util\AppConfiguration;
use Mike42\Escpos\Printer;

class SignUpEndpoint implements IRouteEndpoint
{
    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $firstName = ucwords($_POST['first-name']);
        $lastName = strtoupper($_POST['last-name']);
        $schoolId = $_POST['school-id'];

        $user = new User(0, $firstName, $lastName, $schoolId);
        $this->printTicket($user);

        return "Bienvenue " . $firstName . ' ' . $lastName . ', élève de ' . $schoolId;
    }

    private function printTicket(User $user)
    {
        $userTicker = new UserTicket($user);

        $config = AppConfiguration::Instance();
        $connectorClass = $config->get("printer.connector");
        $port = $config->get("printer.port");
        $connector = new $connectorClass($port);
        $printer = new Printer($connector);
        $printer->initialize();
        $userTicker->printTicket($printer);
        $printer->cut(Printer::CUT_PARTIAL);
        $printer->close();
    }
}