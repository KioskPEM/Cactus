<?php


namespace Banana;


use Banana\Collection\ArrayPath;

class AppContext
{
    private string $method;
    private string $path;
    private string $lang;

    private array $config;

    /**
     * @param string $method
     * @param string $path
     * @param string|null $lang
     * @param array $config
     */
    public function __construct(string $method, string $path, string $lang, array $config)
    {
        $this->method = $method;
        $this->path = $path;
        $this->lang = $lang;
        $this->config = $config;
    }

    public function getConfig(string $path)
    {
        return ArrayPath::get($this->config, $path);
    }

    public function setConfig(string $path, $value)
    {
        ArrayPath::set($this->config, $path, $value);
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}