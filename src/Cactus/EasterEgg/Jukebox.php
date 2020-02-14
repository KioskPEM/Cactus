<?php


namespace Cactus\EasterEgg;


use Cactus\Database\CsvDatabase;
use Cactus\Exception\FileException;
use Cactus\Util\JsonFile;

class Jukebox
{
    private const SONG_PATH = ASSET_PATH . "jukebox.json";

    private array $songs;
    private array $currentSong;

    /**
     * Jukebox constructor.
     * @throws FileException
     */
    private function __construct()
    {
        $this->loadSongs();
    }

    /**
     * @return Jukebox
     * @throws FileException
     */
    public static function Instance(): Jukebox
    {
        static $inst = null;
        if ($inst === null)
            $inst = new Jukebox();
        return $inst;
    }

    public function getCurrentSong(): string
    {
        if (isset($this->currentSong)) {
            $songId = $this->currentSong["id"];
            $song = $this->songs[$songId];
            return $song["name"];
        }
        return false;
    }

    /**
     * @throws FileException
     */
    private function loadSongs()
    {
        $songDatabase = new CsvDatabase("songs");
        $songDatabase->open();
        $this->songs = $songDatabase->get();
        $songDatabase->close();

        if (is_file(self::SONG_PATH)) {
            $this->currentSong = JsonFile::read(self::SONG_PATH);
        }
    }

    public function playRandom(): bool
    {
        $index = array_rand($this->songs);
        return $this->play($index);
    }

    public function play(int $index): bool
    {
        $this->stop();

        if (!array_key_exists($index, $this->songs))
            return false;

        $song = $this->songs[$index];
        $command = $song["command"];

        $descriptor = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];
        $process = proc_open("exec " . $command, $descriptor, $pipes);
        $processStatus = proc_get_status($process);

        if (!$processStatus["running"])
            return false;

        $pid = $processStatus["pid"];
        $this->currentSong = [
            "id" => $index,
            "pid" => $pid
        ];
        return JsonFile::write(self::SONG_PATH, $this->currentSong);
    }

    public function stop()
    {
        if (isset($this->currentSong)) {
            $pid = $this->currentSong["pid"];
            shell_exec("kill $pid");

            unlink(self::SONG_PATH);
            unset($this->currentSong);
        }
    }
}