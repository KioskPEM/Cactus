<?php

namespace Cactus\Endpoint\User;

use Banana\AppContext;
use Banana\Http\HttpCode;
use Banana\Routing\IRouteEndpoint;
use Banana\Routing\Route;
use Cactus\User\Exception\UserException;
use Cactus\User\UserManager;
use Cactus\User\UserTicket;
use Mike42\Escpos\Printer;

class SignUpEndpoint implements IRouteEndpoint
{
    /**
     * @param AppContext $context
     * @inheritDoc
     * @throws UserException
     */
    public function handle(AppContext $context, Route $route, array $parameters): string
    {
        $firstName = ucwords(strtolower($_POST['first-name']));
        $lastName = strtoupper($_POST['last-name']);
        $schoolId = $_POST['school-id'];

        $userManager = $context->getService("Cactus\User\UserManager");
        $user = $userManager->createUser($firstName, $lastName, $schoolId);

        {
            $userTicker = new UserTicket($user);

            $connectorClass = $context->config("printer.connector");
            $port = $context->config("printer.port");
            $connector = new $connectorClass($port);
            $printer = new Printer($connector);
            $printer->initialize();

            $userTicker->printTicket($context, $printer);

            $printer->cut(Printer::CUT_PARTIAL);
            $printer->close();
        }

        $router = $route->getRouter();
        $url = $router->buildUrl("GET", "register.success", null);
        header("Location: " . $url, true, HttpCode::REDIRECT_SEE_OTHER);
        return "";
    }
}