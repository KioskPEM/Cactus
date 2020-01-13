<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "bootstrap.php";

use Cactus\Endpoint\AdminEndpoint;
use Cactus\Endpoint\PrinterEndpoint;
use Cactus\Endpoint\PrintTicketEndpoint;
use Cactus\Http\HttpCode;
use Cactus\Routing\Router;
use Cactus\Template\Render\Pass\EchoPass;
use Cactus\Template\Render\Pass\I18nPass;
use Cactus\Template\Render\Pass\UrlPass;
use Cactus\Template\TemplateManager;
use Cactus\Util\AppConfiguration;
use Cactus\Util\ClientRequest;

try {

    $request = ClientRequest::Instance();

    $router = new Router();
    $rootUrl = AppConfiguration::get("url.root");
    $templateEngine = new TemplateManager([
        "url" => [
            "root" => $rootUrl,
            "static" => AppConfiguration::get("url.static")
        ]
    ]);

    $urlFormat = AppConfiguration::get("url.format");
    $templateEngine->addPass(new UrlPass($router, $urlFormat));
    $templateEngine->addPass(new EchoPass());
    $templateEngine->addPass(new I18nPass());

    $templateEngine->registerTemplate("layout");

    $router->get("admin", "/admin", $templateEngine);

    $router->get("admin_action", "/admin/:action{[a-z]+}", new AdminEndpoint());
    $templateEngine->registerTemplate("admin");

    $printerPort = AppConfiguration::get("printer.port");
    $router->get("printer_print", "/printer/print/:id{[A-Z0-9]+}", new PrintTicketEndpoint($printerPort));
    $router->get("printer_action", "/printer/:action{[a-z]+}", new PrinterEndpoint($printerPort));

    $router->get("error", "/error/:error{[1-5]\d{2}}", $templateEngine);
    $templateEngine->registerTemplate("error");

    $router->get("welcome", "/welcome", $templateEngine);
    $templateEngine->registerTemplate("welcome");

    $router->get("sign-up", "/sign-up", $templateEngine);
    $templateEngine->registerTemplate("sign-up");

    $parameters = [];
    $route = $router->resolveRoute(
        $request->getPath(),
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
        if (ini_get("display_errors")) {
            $parameters['exception']['details'] = "<code style='display: block; text-align: left;'>" . nl2br($e) . "</code>";
        }
        echo $route->call($parameters);

    } catch (Throwable $_) {

        http_response_code(HttpCode::SERVER_ERROR);
        $fallback = file_get_contents(VIEWS_PATH . "fallback.html");
        $exceptionDetails = ini_get("display_errors") ? "<code style='display: block; text-align: left;'>" . nl2br($e) . "</code>" : null;
        echo str_replace("\${route.exception.details}", $exceptionDetails, $fallback);
    }

}