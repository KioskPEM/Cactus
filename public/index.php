<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "bootstrap.php";

use Cactus\EasterEgg\SongPlayer;
use Cactus\Http\HttpCode;
use Cactus\Routing\Exception\RouteException;
use Cactus\Routing\Router;
use Cactus\Template\Render\Pass\EchoPass;
use Cactus\Template\Render\Pass\HandlerPass;
use Cactus\Template\Render\Pass\I18nPass;
use Cactus\Template\Render\Pass\UrlPass;
use Cactus\Template\TemplateManager;
use Cactus\Util\AppConfiguration;
use Cactus\Util\ClientRequest;

try {

    $request = ClientRequest::Instance();
    $config = AppConfiguration::Instance();

    $songPlayer = SongPlayer::Instance();
    $songPlayer->stop();

    $router = new Router();
    $rootUrl = $config->get("url.root");
    $templateEngine = new TemplateManager([
        "url" => [
            "root" => $rootUrl,
            "static" => $config->get("url.static")
        ],
        "home-page" => $config->get("home-page")
    ]);

    $urlFormat = $config->get("url.format");
    $templateEngine->addPass(new EchoPass());
    $templateEngine->addPass(new HandlerPass());
    $templateEngine->addPass(new UrlPass($router, $urlFormat));
    $templateEngine->addPass(new I18nPass());

    $templateEngine->registerTemplate("layout");

    $routesContent = file_get_contents(ASSET_PATH . "routes.json");
    if ($routesContent === false)
        throw new RouteException("Missing routes.json");

    $routes = json_decode($routesContent, true, 512, JSON_THROW_ON_ERROR);
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

    $songPlayer->playAt(0);

    $httpCode = $e->getCode();
    if (!HttpCode::isHttpCode($httpCode))
        $httpCode = HttpCode::SERVER_ERROR;

    http_response_code(HttpCode::SERVER_ERROR);
    $fallback = file_get_contents(VIEWS_PATH . "fallback.html");
    $exceptionDetails = ini_get("display_errors") ? html_entity_decode(nl2br($e)) : null;
    echo str_replace("\${route.exception.details}", $exceptionDetails, $fallback);

}