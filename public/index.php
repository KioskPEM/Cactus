<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "bootstrap.php";

use Cactus\Controller\SignUp\SignUpSelectDepartmentController;
use Cactus\Controller\SignUp\SignUpSelectSchoolController;
use Cactus\Endpoint\AdminEndpoint;
use Cactus\Endpoint\PrinterEndpoint;
use Cactus\Endpoint\PrintTicketEndpoint;
use Cactus\Endpoint\SchoolEndpoint;
use Cactus\Http\HttpCode;
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

    $router = new Router();
    $rootUrl = AppConfiguration::get("url.root");
    $templateEngine = new TemplateManager([
        "url" => [
            "root" => $rootUrl,
            "static" => AppConfiguration::get("url.static")
        ]
    ]);

    $urlFormat = AppConfiguration::get("url.format");
    $templateEngine->addPass(new EchoPass());
    $templateEngine->addPass(new HandlerPass());
    $templateEngine->addPass(new UrlPass($router, $urlFormat));
    $templateEngine->addPass(new I18nPass());

    $templateEngine->registerTemplate("layout");

    $router->get("admin.index", "/admin", $templateEngine);
    $templateEngine->registerTemplate("admin.index");
    $router->get("admin.update", "/admin/update", $templateEngine);
    $templateEngine->registerTemplate("admin.update");
    $router->get("admin_action", "/admin/:action{[a-z_]+}", new AdminEndpoint());

    $printerPort = AppConfiguration::get("printer.port");
    $router->get("printer_print", "/printer/print/:id{[A-Z0-9]+}", new PrintTicketEndpoint($printerPort));
    $router->get("printer_action", "/printer/:action{[a-z]+}", new PrinterEndpoint($printerPort));

    $router->get("schools", "/schools/:region_code{\d{2}}/:department_code{\d{2}}", new SchoolEndpoint());

    $router->get("error", "/error/:error{[1-5]\d{2}}", $templateEngine);
    $templateEngine->registerTemplate("error");

    $router->get("welcome", "/welcome", $templateEngine);
    $templateEngine->registerTemplate("welcome");

    // sign up
    $router->get("sign-up.user-info", "/sign-up", $templateEngine);
    $templateEngine->registerTemplate("sign-up.user-info");
    $router->get("sign-up.select-region", "/sign-up/region", $templateEngine);
    $templateEngine->registerTemplate("sign-up.select-region");
    $router->get("sign-up.select-department", "/sign-up/:region{\d{2}}/department", $templateEngine);
    $templateEngine->registerTemplate("sign-up.select-department", new SignUpSelectDepartmentController());
    $router->get("sign-up.select-grade", "/sign-up/:region{\d{2}}/:department{\d{2}}/grade", $templateEngine);
    $templateEngine->registerTemplate("sign-up.select-grade");
    $router->get("sign-up.select-school", "/sign-up/:region{\d{2}}/:department{\d{2}}/school", $templateEngine);
    $templateEngine->registerTemplate("sign-up.select-school", new SignUpSelectSchoolController());

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