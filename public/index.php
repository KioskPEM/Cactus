<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "bootstrap.php";

use Cactus\EasterEgg\Jukebox;
use Cactus\Http\HttpCode;
use Cactus\Routing\Router;
use Cactus\Template\Render\Pass\EchoPass;
use Cactus\Template\Render\Pass\HandlerPass;
use Cactus\Template\Render\Pass\I18nPass;
use Cactus\Template\Render\Pass\UrlPass;
use Cactus\Template\TemplateManager;
use Cactus\Util\AppConfiguration;
use Cactus\Util\ClientRequest;
use Cactus\Util\JsonUtil;

try {

    $request = ClientRequest::Instance();
    $config = AppConfiguration::Instance();

    $jukebox = Jukebox::Instance();
    $jukebox->stop();

    $router = new Router();
    $rootUrl = $config->get("url.root");
    $templateEngine = new TemplateManager([
        "url" => [
            "root" => $rootUrl,
            "static" => $config->get("url.static")
        ],
        "theme" => $config->get("theme"),
        "home-page" => $config->get("home-page")
    ]);

    $urlFormat = $config->get("url.format");
    $templateEngine->addPass(new EchoPass());
    $templateEngine->addPass(new HandlerPass());
    $templateEngine->addPass(new UrlPass($router, $urlFormat));
    $templateEngine->addPass(new I18nPass());

    $templateEngine->registerTemplate("layout");

    $routes = JsonUtil::read(ASSET_PATH . "routes.json");
    foreach ($routes as $name => $entry) {
        $method = $entry["method"] ?? "GET";
        $path = $entry["path"];

        if (array_key_exists("endpoint", $entry)) {
            $endpoint = new $entry["endpoint"]();
            $router->register($name, $method, $path, $endpoint);
        } else {
            $router->register($name, $method, $path, $templateEngine);

            $controller = array_key_exists("controller", $entry) ? new $entry["controller"]() : null;
            $templateEngine->registerTemplate($name, $controller);
        }
    }

    $parameters = [];
    $route = $router->resolveRoute(
        $request->getPath(),
        $request->getMethod(),
        $parameters
    );
    echo $route->call($parameters);

} catch (Throwable $e) {

    $jukebox->play(0);

    $httpCode = $e->getCode();
    if (!HttpCode::isHttpCode($httpCode))
        $httpCode = HttpCode::SERVER_ERROR;

    http_response_code($httpCode);

    $exceptionDetails = "Error " . $e->getCode() . ": " . $e->getMessage() . "<br>";
    $exceptionDetails .= "-> File: " . $e->getFile() . "<br>";
    $exceptionDetails .= "-> Line: " . $e->getLine() . "<br><br>";
    $exceptionDetails .= "-> Trace: " . "<br>";
    $exceptionDetails .= nl2br(htmlentities($e->getTraceAsString()));

    try {

        $parameters = [];
        $route = $router->resolveRoute("error", "GET", $parameters);
        $parameters['exception']['details'] = $exceptionDetails;
        echo $route->call($parameters);

    } catch (Throwable $_) {

        $fallback = file_get_contents(VIEWS_PATH . "fallback.html");
        echo str_replace("\${route.exception.details}", $exceptionDetails, $fallback);

    }


}