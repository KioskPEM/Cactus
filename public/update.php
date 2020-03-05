<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "bootstrap.php";

use Cactus\Exception\FileException;
use Cactus\Util\AppConfiguration;
use Cactus\Util\JsonUtil;

define("RELEASE_PATH", ASSET_PATH . "release.json");

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
        CURLOPT_BINARYTRANSFER => true,
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


function downloadFile($url, $destination)
{
    $fileHandler = fopen($destination, "w");
    if (!$fileHandler)
        report(500, "Unable to open file handler: " . $destination);

    /** @var false|resource $ch */
    $curlHandler = curl_init();
    curl_setopt_array($curlHandler, [
        CURLOPT_URL => $url,
        CURLOPT_FILE => $fileHandler,
        CURLOPT_BINARYTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => "Cactus",
        CURLOPT_CONNECTTIMEOUT => 60,
        CURLOPT_TIMEOUT => 60
    ]);

    if (!curl_exec($curlHandler)) {
        curl_close($curlHandler);
        fclose($fileHandler);

        $error = curl_error($curlHandler);
        report(500, "Unable to retrieve " . $url . ": " . $error);
    }

    curl_close($curlHandler);
    fclose($fileHandler);
}

try {
    $releaseInfo = JsonUtil::read(RELEASE_PATH);
    $currentVersion = $releaseInfo["id"];
} catch (FileException $e) {
    report(500, "Unable to read release.json");
}

$compareUrl = "https://api.github.com/repos/TheWhoosher/Cactus/compare/" . $currentVersion . "...master";
$rawDifferences = download($compareUrl);
$differences = JsonUtil::decode($rawDifferences);

$commits = $differences["commits"];
if (empty($commits))
    report(200, "Cactus is already up-to-date");

$files = $differences["files"];
foreach ($files as $file) {
    $status = $file["status"];
    $fileName = $file["filename"];
    $filePath = ROOT . $fileName;

    if ($status === "removed")
        unlink($filePath);
    else if ($status === "renamed") {
        $previousFilename = $file["previous_filename"];
        rename(ROOT . $previousFilename, $filePath);
    } else if ($status === "added" || $status === "modified") {
        $contentUrl = $file["raw_url"];
        downloadFile($contentUrl, $filePath);
    } else
        report(500, "Unknown status: " . $status);
}

$lastCommit = end($differences["commits"]);
$lastCommitId = $lastCommit["sha"];
JsonUtil::write(RELEASE_PATH, [
    "id" => $lastCommitId
]);

// save the overwritten config
$config = AppConfiguration::Instance();
$savedConfig = $config->getConfig();
$config->reload();
$config->apply($savedConfig);
$config->save();

report(200, "Cactus is now up-to-date");