<?php


namespace Banana\Collection;


class ArrayPath
{
    private function __construct()
    {
    }

    public static function has(array $subject, string $path, string $delimiter = '.'): bool
    {
        return (bool)ArrayPath::get($subject, $path, $delimiter);
    }

    /**
     * @param array $subject
     * @param string $path
     * @param string $delimiter
     * @return mixed|false
     */
    public static function get(array $subject, string $path, string $delimiter = '.')
    {
        $temp = &$subject;
        foreach (explode($delimiter, $path) as $arrayKey) {
            if (isset($temp[$arrayKey]))
                $temp = &$temp[$arrayKey];
            else
                return false;
        }
        return $temp;
    }

    /**
     * @param array $subject
     * @param string $path
     * @param string $delimiter
     * @return void
     */
    public static function delete(array &$subject, string $path, string $delimiter = '.'): void
    {
        $parts = explode($delimiter, $path);

        $temp = &$subject;
        while (true) {
            $part = array_shift($parts);

            if (empty($parts)) {
                unset($temp[$part]);
                return;
            }

            if (!array_key_exists($part, $temp))
                return;
            $temp = &$temp[$part];
        }
    }

    /**
     * @param array $subject
     * @param string $path
     * @param $value
     * @param string $delimiter
     * @return mixed
     */
    public static function set(array &$subject, string $path, $value, string $delimiter = '.')
    {
        $parts = explode($delimiter, $path);

        $temp = &$subject;
        while (true) {
            $part = array_shift($parts);

            if (empty($parts)) {
                return $temp[$part] = $value;
            }

            if (!array_key_exists($part, $temp))
                $temp[$part] = [];
            $temp = &$temp[$part];
        }
    }
}