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
        return JsonFile::write(self::CONFIG_PATH, $this->config);
    }

    public function reload(): bool
    {
        $config = JsonFile::read(self::CONFIG_PATH);
        if ($config) {
            $this->config = $config;
            return true;
        }

        return false;
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