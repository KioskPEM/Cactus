<?php


namespace Cactus\EasterEgg;


use Banana\IO\FileException;
use Banana\Serialization\CsvException;
use Banana\Serialization\CsvSerializer;

class Jukebox
{
    private const PID_PATH = ASSET_PATH . "jukebox.pid";

    private function __construct()
    {
    }

    /**
     * @throws FileException
     * @throws CsvException
     */
    public static function play(?int $index = null): ?string
    {
        $songs = CsvSerializer::deserializeFile(DATA_PATH . "songs.csv");
        if ($index != null && !array_key_exists($index, $songs))
            return null;

        $song = ($index !== null) ? $songs[$index] : array_rand($songs);
        $command = $song["command"];

        $descriptor = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];
        $process = proc_open("exec " . $command, $descriptor, $pipes);
        $processStatus = proc_get_status($process);
        if (!$processStatus["running"])
            return null;

        file_put_contents(self::PID_PATH, $processStatus["pid"]);
        return $song["name"];
    }

    public static function stop()
    {
        if (!is_file(self::PID_PATH))
            return;

        $pid = file_get_contents(self::PID_PATH);
        if ($pid === false)
            return;

        shell_exec("kill $pid");
        unlink(self::PID_PATH);
    }
}