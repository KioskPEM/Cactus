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
            "CALLED WITH " . "\"sh " . self::ERROR_SONG_FILE . '"'
        );
        $descriptor = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];
        $process = proc_open("sh " . self::ERROR_SONG_FILE, $descriptor, $pipes);
        $processStatus = proc_get_status($process);
        var_dump($processStatus);

        // Read the responses if you want to look at them
        $stdout = fread($pipes[1], 1024);
        var_dump($stdout);
        $stderr = fread($pipes[2], 1024);
        var_dump($stderr);

        if ($processStatus["running"])
            $_SESSION["song_pid"] = $processStatus["pid"];
    }
}