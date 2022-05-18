<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "bootstrap.php";

use Banana\IO\FileException;
use Banana\Serialization\JsonSerializer;

const RELEASE_PATH = ASSET_PATH . "release.json";

function report(int $code, string $message)
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        "message" => $message
    ]);
    die($code === 200 ? 0 : 1);
}

function download($url)
{
    /** @var false|resource $ch */
    $curlHandler = curl_init();
    curl_setopt_array($curlHandler, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => "Cactus",
        CURLOPT_CONNECTTIMEOUT => 60,
        CURLOPT_TIMEOUT => 60
    ]);

    /** @var bool|string $downloadData */
    $downloadData = curl_exec($curlHandler);
    if (!$downloadData) {
        $error = curl_error($curlHandler);
        report(500, "Unable to retrieve " . $url . ": " . $error);
    }

    curl_close($curlHandler);
    return $downloadData;
}


try {
    $release = JsonSerializer::deserializeFile(RELEASE_PATH);
    $diffContent = download("https://api.github.com/repos/KioskPEM/Cactus/compare/" . $release["id"] . "...master");
    $diff = JsonSerializer::deserialize($diffContent);

    $commits = $diff["commits"];
    if (empty($commits)) {
        report(200, "Cactus is already up-to-date");
        return;
    }

    $files = $diff["files"];
    $fileCount = count($files);

    // load config before overwriting it
    $config = JsonSerializer::deserializeFile(CONFIG_PATH);

    foreach ($files as $file) {
        $status = $file["status"];
        $fileName = $file["filename"];
        $filePath = ROOT . $fileName;

        if ($status === "removed") {
            unlink($filePath);
        } else if ($status === "renamed") {
            $fileParent = dirname($filePath);
            if (!is_dir($fileParent))
                mkdir($fileParent, 0755, true);

            $previousFilename = $file["previous_filename"];
            rename(ROOT . $previousFilename, $filePath);
        } else if ($status === "added" || $status === "modified") {
            $contentUrl = $file["raw_url"];
            $destinationParent = dirname($filePath);
            if (!is_dir($destinationParent))
                mkdir($destinationParent, 0755, true);

            $fileHandler = fopen($filePath, "w");
            if ($fileHandler === false) {
                throw new FileException("Unable to open file " . $filePath);
            }

            $curlHandler = curl_init();
            curl_setopt_array($curlHandler, [
                CURLOPT_URL => $contentUrl,
                CURLOPT_FILE => $fileHandler,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => "Cactus",
                CURLOPT_CONNECTTIMEOUT => 60,
                CURLOPT_TIMEOUT => 60
            ]);

            if (!curl_exec($curlHandler)) {
                curl_close($curlHandler);
                fclose($fileHandler);

                $error = curl_error($curlHandler);
                report(500, "Unable to retrieve " . $contentUrl . ": " . $error);
            }

            curl_close($curlHandler);
            fclose($fileHandler);
        } else
            report(500, "Unknown status: " . $status);
    }

    $lastCommit = end($diff["commits"]);
    JsonSerializer::serializeFile(RELEASE_PATH, [
        "id" => $lastCommit["sha"]
    ]);

    // save the overwritten config
    $defaultConfig = JsonSerializer::deserializeFile(CONFIG_PATH);
    $config = array_merge_recursive($defaultConfig, $config);
    JsonSerializer::serializeFile(CONFIG_PATH, $config);

    report(200, "Cactus is now up-to-date (" . $fileCount . " file(s) updated)");
} catch (FileException|JsonException $e) {
    report(500, "Failed to update (" . $e->getMessage() . ')');
}