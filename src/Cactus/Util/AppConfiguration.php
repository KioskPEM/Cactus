<?php


namespace Cactus\Util;


class AppConfiguration
{
    private const CONFIG_PATH = ASSET_PATH . "config.json";

    private array $config;

    private function __construct()
    {
    }

    /**
     * Gets the client's request
     *
     * @return AppConfiguration
     */
    public static function Instance(): AppConfiguration
    {
        static $inst = null;
        if ($inst === null)
            $inst = new AppConfiguration();
        return $inst;
    }

    private function ensureLoad()
    {
        if (!isset($this->config))
            $this->reload();
    }

    public function apply(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    public function delete(string $path)
    {
        ArrayPath::delete($this->config, $path);
    }

    public function set(string $path, $value)
    {
        ArrayPath::set($this->config, $path, $value);
    }

    public function get(string $path)
    {
        $this->ensureLoad();
        return ArrayPath::get($this->config, $path);
    }

    public function save(): bool
    {
        $content = json_encode($this->config, JSON_PRETTY_PRINT, 512);
        if ($content === false)
            return false;
        return file_put_contents(self::CONFIG_PATH, $content);
    }

    public function reload(): bool
    {
        $content = file_get_contents(self::CONFIG_PATH);
        if ($content === false)
            return false;

        $config = json_decode($content, true, 512);
        if ($config === null)
            return false;

        $this->config = $config;
        return true;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $this->ensureLoad();
        return $this->config;
    }

}