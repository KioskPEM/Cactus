<?php


namespace Cactus\EasterEgg;


use Cactus\Database\CsvDatabase;
use Cactus\Util\AppConfiguration;

class Jukebox
{
    private array $songs;

    private function __construct()
    {
        $this->loadSongs();
    }

    public static function Instance(): Jukebox
    {
        static $inst = null;
        if ($inst === null)
            $inst = new Jukebox();
        return $inst;
    }

    public function getCurrentSong(): string
    {
        $config = AppConfiguration::Instance();
        return $config->get("song.name");
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
        $index = array_rand($this->songs);
        return $this->playIndexed($index);
    }

    public function playIndexed(int $index): bool
    {
        $song = $this->songs[$index];
        $name = $song["name"];
        $command = $song["command"];
        return $this->play($name, $command);
    }

    public function play(string $name, string $command)
    {
        $this->stop();

        $descriptor = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];
        $process = proc_open("exec " . $command, $descriptor, $pipes);
        $processStatus = proc_get_status($process);

        if (!$processStatus["running"])
            return false;

        $config = AppConfiguration::Instance();
        $config->set("song.name", $name);
        $config->set("song.pid", $processStatus["pid"]);
        $config->save();
        return true;
    }

    public function stop()
    {
        if (!array_key_exists("song", $_SESSION))
            return;

        $config = AppConfiguration::Instance();
        $process = $config->get("song.pid");
        shell_exec("kill $process");
        $config->delete("song");
        $config->save();
    }
}