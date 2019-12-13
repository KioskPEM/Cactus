<?php


namespace Cactus\Util;


class ClientRequest
{
    private string $method;
    private string $url;
    private string $lang;

    private function __construct()
    {
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->url = $_GET["url"] ?? "/";
        $this->lang = $_GET["lang"] ?? "en";
    }

    /**
     * Gets the client's request
     *
     * @return ClientRequest
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null)
            $inst = new ClientRequest();
        return $inst;
    }

    /**
     * @return mixed|string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed|string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed|string
     */
    public function getLang()
    {
        return $this->lang;
    }

}