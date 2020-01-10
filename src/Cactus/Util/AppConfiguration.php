<?php


namespace Cactus\Util;


class AppConfiguration
{
    private const CONFIG_PATH = ASSET_PATH . "config.json";

    private static array $config;

    private function __construct()
    {
    }

    private static function ensureLoad() {
        if (!isset(self::$config))
            self::reload();
    }

    public static function apply(array $config)
    {
        self::$config = array_merge(self::$config, $config);
    }

    public static function set(string $path, $value)
    {
        ArrayPath::set(self::$config, $path, $value);
    }

    public static function get(string $path)
    {
        self::ensureLoad();
        return ArrayPath::get(self::$config, $path);
    }

    public static function save(): bool
    {
        $content = json_encode(self::$config, JSON_PRETTY_PRINT, 512);
        if ($content === false)
            return false;
        return file_put_contents(self::CONFIG_PATH, $content);
    }

    public static function reload(): bool
    {
        $content = file_get_contents(self::CONFIG_PATH);
        if ($content === false)
            return false;

        $config = json_decode($content, true, 512);
        if ($config === null)
            return false;

        self::$config = $config;
        return true;
    }

    /**
     * @return array
     */
    public static function getConfig(): array
    {
        self::ensureLoad();
        return self::$config;
    }

}