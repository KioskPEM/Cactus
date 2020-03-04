<?php


namespace Cactus\Util;


use Cactus\Exception\FileException;

class JsonUtil
{
    private function __construct()
    {
    }

    /**
     * @param string $json
     * @return array|false
     */
    public static function decode(string $json): array
    {
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $path
     * @return array|false
     * @throws FileException
     */
    public static function read(string $path): array
    {
        $fileContent = file_get_contents($path);
        if ($fileContent === false)
            throw new FileException("File not found" . $path);
        return json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $path
     * @param array $data
     * @return bool
     */
    public static function write(string $path, array $data): bool
    {
        $fileContent = json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR, 512);
        return file_put_contents($path, $fileContent);
    }
}