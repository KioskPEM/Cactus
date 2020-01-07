<?php


namespace Cactus\Endpoint;


use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use ZipArchive;

class UpdateEndpoint implements IRouteEndpoint
{
    private const APP_URL = "https://github.com/TheWhoosher/Cactus/archive/master.zip";

    private string $archiveFile;
    private ZipArchive $archive;

    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $tmpDir = sys_get_temp_dir();
        $this->archiveFile = tempnam($tmpDir, "Cactus");

        $options = array(
            CURLOPT_URL => self::APP_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => "Cactus",
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_TIMEOUT => 60
        );
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $archiveData = curl_exec($ch);

        if (!$archiveData) {
            $error = curl_error($ch);
            curl_close($ch);
            return $this->release("Unable to download the latest version of Cactus: " . $error);
        }

        curl_close($ch);

        if (!file_put_contents($this->archiveFile, $archiveData)) {
            return $this->release("Unable to save the archive to the disk.");
        }

        $this->archive = new ZipArchive();
        if (!$this->archive->open($this->archiveFile)) {
            return $this->release("Unable to open the zip archive. The download is probably corrupted.");
        }

        $destination = tempnam($tmpDir, 'Cactus');
        if (!$this->archive->extractTo($destination)) {
            return $this->release("Unable to extract the zip archive. It's probably permission-related?");
        }

        if (!rename($destination, ROOT)) {
            return $this->release("Unable to move the up-to-date application to the Web directory.");
        }

        return $this->release("Cactus is now up-to-date");
    }

    private function release(string $message): string
    {
        if (file_exists($this->archiveFile))
            unlink($this->archiveFile);

        if (isset($this->archive))
            $this->archive->close();

        return $message;
    }
}