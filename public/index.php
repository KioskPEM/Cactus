<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "bootstrap.php";

use Cactus\App;
use Cactus\Http\HttpCode;
use Cactus\Routing\Router;
use Cactus\Template\Pass\EchoPass;
use Cactus\Template\Pass\I18nPass;
use Cactus\Template\TemplateManager;
use Cactus\Util\ClientRequest;

try {
    $config = json_decode(file_get_contents(ASSET_PATH . 'config.json'), true, 512, JSON_THROW_ON_ERROR);

    $request = ClientRequest::Instance();

    $router = new Router();
    $templateEngine = new TemplateManager();

    $app = new App($config);

    $templateEngine->addPass(new EchoPass());
    $templateEngine->addPass(new I18nPass());
    $templateEngine->registerTemplate("layout");

    $router->get("error", "/error/:error{[1-5]\d{2}}", $templateEngine);
    $templateEngine->registerTemplate("error");

    $parameters = [];
    $route = $router->resolveRoute(
        $request->getUrl(),
        $request->getMethod(),
        $parameters
    );
    echo $route->call($parameters);

} catch (Throwable $e) {

    $httpCode = $e->getCode();
    if (!HttpCode::isHttpCode($httpCode))
        $httpCode = HttpCode::SERVER_ERROR;

    try {

        http_response_code($httpCode);
        $parameters = [];
        $route = $router->resolveRoute("error/" . $httpCode, "GET", $parameters);
        echo $route->call($parameters);

    } catch (Throwable $e) {

        http_response_code(HttpCode::SERVER_ERROR);
        $fallback = file_get_contents(VIEWS_PATH . "fallback.html");
        $exceptionDetails = ini_get("display_errors") ? "<hr/>" . nl2br($e->getTraceAsString()) : null;
        echo str_replace("%{exception.details}", $exceptionDetails, $fallback);
    }

}