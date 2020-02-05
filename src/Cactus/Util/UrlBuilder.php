<?php


namespace Cactus\Util;


use Cactus\Routing\Exception\RouteNotFoundException;
use Cactus\Routing\Router;

class UrlBuilder
{
    private function __construct()
    {
    }

    public static function Instance(): UrlBuilder
    {
        static $inst = null;
        if ($inst === null)
            $inst = new UrlBuilder();
        return $inst;
    }

    /***
     * @param Router $router
     * @param string $page
     * @param array $parameters
     * @return string
     * @throws RouteNotFoundException
     */
    public function build(Router $router, string $page, array $parameters): string
    {
        $config = AppConfiguration::Instance();
        $request = ClientRequest::Instance();

        $format = $config->get("url.format");
        $lang = $request->getLang();
        $path = $router->buildUrl("GET", $page, $parameters);
        return sprintf($format, $lang, $path);
    }

}