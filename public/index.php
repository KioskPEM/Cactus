<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "bootstrap.php";

use Cactus\App;
use Cactus\Http\HttpCode;
use Cactus\Routing\Router;

try {
    $config = null;

    $router = new Router();

    $app = new App($config);
} catch (Throwable $e) {

    http_response_code(HttpCode::SERVER_ERROR);
    require_once VIEWS_PATH . "fallback.html";

}