<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "bootstrap.php";

use Banana\AppContext;
use Banana\Http\HttpCode;
use Banana\Localization\LocalizationManager;
use Banana\Routing\Router;
use Banana\Serialization\JsonSerializer;
use Banana\Template\Render\Pass\EchoPass;
use Banana\Template\Render\Pass\HandlerPass;
use Banana\Template\Render\Pass\LocalizePass;
use Banana\Template\Render\Pass\UrlPass;
use Banana\Template\TemplateManager;
use Cactus\EasterEgg\Jukebox;

try {
    $config = JsonSerializer::deserializeFile(ASSET_PATH . "config.json");
    $context = new AppContext(
        $_SERVER["REQUEST_METHOD"],
        $_GET["path"],
        $_GET["lang"],
        $config
    );

    $router = new Router(
        $context->getConfig("url.format"),
        $context->getLang()
    );
    $localizationManager = new LocalizationManager();
    $templateEngine = new TemplateManager([
        "url" => [
            "root" => $context->getConfig("url.root"),
            "static" => $context->getConfig("url.static")
        ],
        "theme" => $context->getConfig("theme"),
        "home-page" => $context->getConfig("home-page")
    ]);

    Jukebox::stop();

    $templateEngine->addPass(new EchoPass());
    $templateEngine->addPass(new HandlerPass());
    $templateEngine->addPass(new UrlPass());
    $templateEngine->addPass(new LocalizePass($localizationManager));

    $templateEngine->registerTemplate("layout");

    $routeContent = file_get_contents(ASSET_PATH . "routes.json");
    $routes = JsonSerializer::deserialize($routeContent);
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
        $context->getPath(),
        $context->getMethod(),
        $parameters
    );
    echo $route->call($context, $parameters);

} catch (Throwable $e) {

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
        echo $route->call($context, $parameters);

    } catch (Throwable $_) {

        $fallback = file_get_contents(VIEWS_PATH . "fallback.html");
        echo str_replace("\${route.exception.details}", $exceptionDetails, $fallback);

    }


}