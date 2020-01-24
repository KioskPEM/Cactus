<?php


namespace Cactus\EasterEgg;


use Cactus\Database\CsvDatabase;

class SongPlayer
{
    private array $songs;

    private function __construct()
    {
        $this->loadSongs();
    }

    public static function Instance(): SongPlayer
    {
        static $inst = null;
        if ($inst === null)
            $inst = new SongPlayer();
        return $inst;
    }

    private function loadSongs()
    {
        $songDatabase = new CsvDatabase("songs");
        $songDatabase->open();
        $this->songs = $songDatabase->get();
        $songDatabase->close();
    }

    public function playRandom(): bool
    {
        $song = array_rand($this->songs);
        return $this->play($this->songs[$song]);
    }

    public function play(array $song): bool
    {
        $this->stop();

        $descriptor = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];
        $command = $song["command"];
        $process = proc_open($command, $descriptor, $pipes);
        $processStatus = proc_get_status($process);

        if (!$processStatus["running"])
            return false;

        $_SESSION["song_pid"] = $processStatus["pid"];
        return true;
    }

    public function stop()
    {
        if (!array_key_exists("song_pid", $_SESSION))
            return;

        $process = $_SESSION["song_pid"];
        shell_exec("kill -9 $process");
        unset($_SESSION["song_pid"]);
    }
}