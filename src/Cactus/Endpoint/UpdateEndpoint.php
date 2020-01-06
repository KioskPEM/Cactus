<?php


namespace Cactus\Endpoint;


use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use ZipArchive;

class UpdateEndpoint implements IRouteEndpoint
{
    private const APP_URL = "https://github.com/TheWhoosher/Cactus/archive/master.zip";

    private string $archiveFile;

    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $tmpDir = sys_get_temp_dir();
        $this->archiveFile = tempnam($tmpDir, "Cactus");

        $options = array(
            CURLOPT_FILE => $this->archiveFile,
            CURLOPT_URL => self::APP_URL,
        );
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $success = curl_exec($ch);
        curl_close($ch);

        if (!$success) {
            $info = curl_getinfo($ch);
            return $this->release("Unable to download the latest version of Cactus: " . $info);
        }

        $zip = new ZipArchive();
        if (!$zip->open($this->archiveFile)) {
            return $this->release("Unable to open the zip archive. The download is probably corrupted");
        }

        if (!$zip->extractTo(ROOT)) {
            $zip->close();
            return $this->release("Unable to extract the zip archive. It's probably permission-related?");
        }

        $zip->close();

        return "Cactus is now up-to-date";
    }

    private function release(string $error): string
    {
        unlink($this->archiveFile);
        return $error;
    }
}