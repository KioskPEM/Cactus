<?php


namespace Cactus;

class ErrorSongPlayer
{
    private const ERROR_SONG_FILE = DATA_PATH . "error_song.sh";

    private function __construct()
    {
    }

    public static function checkStatus()
    {
        if (!array_key_exists("song_pid", $_SESSION))
            return;

        $process = $_SESSION["song_pid"];
        shell_exec("kill -9 $process");
        unset($_SESSION["song_pid"]);
    }

    public static function play()
    {
        var_dump(
            "CALLED WITH " . "exec sh " . self::ERROR_SONG_FILE
        );
        $descriptor = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];
        $process = proc_open("exec sh " . self::ERROR_SONG_FILE, $descriptor, $pipes);
        $processStatus = proc_get_status($process);
        if ($processStatus["running"])
            $_SESSION["song_pid"] = $processStatus["pid"];
    }
}