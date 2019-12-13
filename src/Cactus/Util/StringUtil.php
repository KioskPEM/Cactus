<?php

namespace Cactus\Util;

class StringUtil
{
    private const PARAM_OPEN_CHAR = '{';
    private const PARAM_CLOSE_CHAR = '}';

    private function __construct()
    {
    }

    /**
     * Return true if the $haystack start with the $needle
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function startWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * @param string $subject
     * @param array $parameters
     * @return string
     */
    public static function format(string $subject, array $parameters): string
    {
        if (empty($parameters))
            return $subject;

        $formatted = '';
        $length = strlen($subject);

        $bracketIndex = -1;
        $bracketLength = 0;
        $escaped = false;

        for ($i = 0; $i < $length; $i++) {
            $char = $subject[$i];
            if ($bracketIndex != -1) {
                if ($char === self::PARAM_CLOSE_CHAR) {
                    if ($bracketLength > 0) {
                        $key = substr($subject, $bracketIndex + 1, $bracketLength);
                        $formatted .= $parameters[$key];
                    }

                    $bracketIndex = -1;
                    $bracketLength = 0;
                } else {
                    $bracketLength++;
                }
            } else if (!$escaped && $char === self::PARAM_OPEN_CHAR)
                $bracketIndex = $i;
            else
                $formatted .= $char;

            $escaped = ($char === '\\');
        }

        return $formatted;
    }
}