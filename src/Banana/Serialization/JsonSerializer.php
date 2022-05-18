<?php


namespace Banana\Serialization;


use Banana\IO\FileException;
use JsonException;

class JsonSerializer
{
    private function __construct()
    {
    }

    /**
     * @param string $json
     * @return mixed
     * @throws JsonException
     */
    public static function deserialize(string $json)
    {
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param mixed $data
     * @return string
     * @throws JsonException
     */
    public static function serialize($data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR, 512);
    }

    /**
     * @param string $path
     * @return array|false
     * @throws JsonException
     * @throws FileException
     */
    public static function deserializeFile(string $path): array
    {
        $json = file_get_contents($path);
        if ($json === false) throw new FileException("Failed to read file");
        return self::deserialize($json);
    }

    /**
     * @param string $path
     * @param array $data
     * @throws JsonException
     * @throws FileException
     */
    public static function serializeFile(string $path, array $data): void
    {
        $json = self::serialize($data);
        $success = file_put_contents($path, $json);
        if ($success === false) throw new FileException("Failed to write file");
    }
}