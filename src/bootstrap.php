<?php
// debug mode
error_reporting(E_ALL);
ini_set("display_errors", 1);
//

define("ROOT", dirname(__DIR__) . DIRECTORY_SEPARATOR);
define("SOURCE_PATH", ROOT . "src" . DIRECTORY_SEPARATOR);
define("STATIC_PATH", ROOT . "static" . DIRECTORY_SEPARATOR);
define("ASSET_PATH", ROOT . "assets" . DIRECTORY_SEPARATOR);
define("DATA_PATH", ASSET_PATH . "data" . DIRECTORY_SEPARATOR);
define("VIEWS_PATH", ASSET_PATH . "views" . DIRECTORY_SEPARATOR);

spl_autoload_register(function ($className) {
    $className = ltrim($className, '\\');
    $fileName = SOURCE_PATH;
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require $fileName;
});

set_exception_handler(function ($e) {
    if (ini_get("display_errors"))
        echo $e;

    http_response_code(500);
    require_once VIEWS_PATH . "fallback.html";
});