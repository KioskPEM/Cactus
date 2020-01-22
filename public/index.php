<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "bootstrap.php";

use Cactus\ErrorSongPlayer;
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

    ErrorSongPlayer::checkStatus();

    $request = ClientRequest::Instance();

    $router = new Router();
    $rootUrl = AppConfiguration::get("url.root");
    $templateEngine = new TemplateManager([
        "url" => [
            "root" => $rootUrl,
            "static" => AppConfiguration::get("url.static")
        ],
        "home-page" => AppConfiguration::get("home-page")
    ]);

    $urlFormat = AppConfiguration::get("url.format");
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

    /*$router->get("admin.admin-index", "/admin", $templateEngine);
    $templateEngine->registerTemplate("admin.admin-index");
    $router->get("admin.update", "/admin/update", $templateEngine);
    $templateEngine->registerTemplate("admin.update");
    $router->get("admin_action", "/admin/:action{[a-z_]+}", new AdminEndpoint());

    $printerPort = AppConfiguration::get("printer.port");
    $router->get("printer_print", "/printer/print/:id{[A-Z0-9]+}", new PrintTicketEndpoint($printerPort));
    $router->get("printer_action", "/printer/:action{[a-z]+}", new PrinterEndpoint($printerPort));

    $router->get("error", "/error/:error{[1-5]\d{2}}", $templateEngine);
    $templateEngine->registerTemplate("error");

    $router->get("welcome", "/welcome", $templateEngine);
    $templateEngine->registerTemplate("welcome");

    // sign up
    $router->get("search-school.region", "/sign-up/region", $templateEngine);
    $templateEngine->registerTemplate("search-school.region");
    $router->get("sign-up.select-department", "/sign-up/region/:region{\d{2}}/department", $templateEngine);
    $templateEngine->registerTemplate("sign-up.select-department", new SelectDepartmentController());
    $router->get("sign-up.select-school-type", "/sign-up/region/:region{\d{2}}/department/:department{\d{2}}/school/type", $templateEngine);
    $templateEngine->registerTemplate("sign-up.select-school-type");
    $router->get("sign-up.select-school", "/sign-up/region/:region{\d{2}}/department/:department{\d{2}}/:school_type{college|high_school}", $templateEngine);
    $templateEngine->registerTemplate("sign-up.select-school", new SelectSchoolController());
    $router->get("sign-up.user-info", "/sign-up/:school_id{\d{7}[A-Z]}", $templateEngine);
    $templateEngine->registerTemplate("sign-up.user-info");*/

    $parameters = [];
    $route = $router->resolveRoute(
        $request->getPath(),
        $request->getMethod(),
        $parameters
    );
    echo $route->call($parameters);

} catch (Throwable $e) {

    ErrorSongPlayer::play();

    $httpCode = $e->getCode();
    if (!HttpCode::isHttpCode($httpCode))
        $httpCode = HttpCode::SERVER_ERROR;

    http_response_code(HttpCode::SERVER_ERROR);
    $fallback = file_get_contents(VIEWS_PATH . "fallback.html");
    $exceptionDetails = ini_get("display_errors") ? html_entity_decode(nl2br($e)) : null;
    echo str_replace("\${route.exception.details}", $exceptionDetails, $fallback);

}