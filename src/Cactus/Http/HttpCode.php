<?php


namespace Cactus\Http;


class HttpCode
{
    const SUCCESS_OK = 200;
    const SUCCESS_NO_CONTENT = 204;

    const REDIRECT_FOUND = 303;

    public const CLIENT_BAD_REQUEST = 400;
    public const CLIENT_NOT_FOUND = 404;
    public const CLIENT_METHOD_NOT_ALLOWED = 405;

    public const SERVER_ERROR = 500;

    public static function isHttpCode(int $code)
    {
        switch ($code) {
            case self::SUCCESS_OK:
            case self::SUCCESS_NO_CONTENT:

            case self::REDIRECT_FOUND:

            case self::CLIENT_BAD_REQUEST:
            case self::CLIENT_NOT_FOUND:
            case self::CLIENT_METHOD_NOT_ALLOWED:

            case self::SERVER_ERROR:

                return true;
            default:
                return false;
        }
    }
}