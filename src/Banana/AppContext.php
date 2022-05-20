<?php


namespace Banana;


use Banana\Collection\ArrayPath;

class AppContext
{
    private string $method;
    private string $path;
    private string $lang;

    private array $services;
    private array $config;
    private array $i18n;

    /**
     * @param string $method
     * @param string $path
     * @param string|null $lang
     * @param array $config
     * @param array $i18n
     */
    public function __construct(string $method, string $path, string $lang, array $config, array $i18n)
    {
        $this->method = $method;
        $this->path = $path;
        $this->lang = $lang;

        $this->config = $config;
        $this->i18n = $i18n;
    }

    /**
     * Registers a service
     *
     * @param object $service
     * @return void
     */
    public function registerService(object $service) {
        $name = get_class($service);
        $this->services[$name] = $service;
    }

    /**
     * Gets a service from its class name
     *
     * @param string $name
     * @return mixed
     */
    public function getService(string $name)
    {
        return $this->services[$name];
    }

    /**
     * Translates a string
     *
     * @param string $path The path
     * @param string $lineDelimiter The new-line delimiter
     * @return array|mixed|string
     */
    public function translate(string $path, string $lineDelimiter = "<br>")
    {
        $value = ArrayPath::get($this->i18n, $path);
        if ($value === false)
            return $path;

        if (is_array($value)) {
            if (count($value) == 1)
                return $value[0];

            $temp = '';
            foreach ($value as $entry)
                $temp .= $entry . $lineDelimiter;
            $value = $temp;
        }

        return $value;
    }

    /**
     * Gets or sets a config value
     *
     * @param string $path the path
     * @param ?mixed $value if null, the current value is returned
     * @return array|false|mixed
     */
    public function config(string $path, $value = null)
    {
        if (is_null($value)) return ArrayPath::get($this->config, $path);
        return ArrayPath::set($this->config, $path, $value);
    }

    public function getConfig(): array
    {
        return $this->config;
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