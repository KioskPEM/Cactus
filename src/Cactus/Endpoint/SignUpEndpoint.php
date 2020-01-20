<?php

namespace Cactus\Endpoint;

use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;

class SignUpEndpoint implements IRouteEndpoint
{
    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        return "Bienvenue " . $_POST['first-name'] . ' ' . $_POST['last-name'] . ', élève de ' . $_POST['school-id'];
    }
}