<?php

namespace Cactus\Endpoint;

use Cactus\Http\HttpCode;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Cactus\User\Exception\UserException;
use Cactus\User\User;
use Cactus\User\UserManager;
use Cactus\User\UserTicket;
use Cactus\Util\AppConfiguration;
use Cactus\Util\UrlBuilder;
use Mike42\Escpos\Printer;

class SignUpEndpoint implements IRouteEndpoint
{
    /**
     * @inheritDoc
     * @throws UserException
     */
    public function handle(Route $route, array $parameters): string
    {
        $firstName = ucwords($_POST['first-name']);
        $lastName = strtoupper($_POST['last-name']);
        $schoolId = $_POST['school-id'];

        $userManager = UserManager::Instance();
        $user = $userManager->createUser($firstName, $lastName, $schoolId);
        $this->printTicket($user);

        $router = $route->getRouter();
        $urlBuilder = UrlBuilder::Instance();
        $homePage = $urlBuilder->build($router, "sign-up.thanks", $parameters);
        header("Location: " . $homePage, true, HttpCode::REDIRECT_SEE_OTHER);
        return "";
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