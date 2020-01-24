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

    public function getCurrentSong(): string
    {
        return $_SESSION["song"]["name"] ?? false;
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

    public function playAt(int $song): bool
    {
        return $this->play($this->songs[$song]);
    }

    private function play(array $song): bool
    {
        $this->stop();

        $descriptor = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];
        $command = $song["command"];
        $process = proc_open("exec " . $command, $descriptor, $pipes);
        $processStatus = proc_get_status($process);

        if (!$processStatus["running"])
            return false;

        $_SESSION["song"] = [
            "name" => $song["name"],
            "pid" => $processStatus["pid"]
        ];
        return true;
    }

    public function stop()
    {
        if (!array_key_exists("song", $_SESSION))
            return;

        $process = $_SESSION["song"]["pid"];
        shell_exec("kill $process");
        unset($_SESSION["song"]);
    }
}