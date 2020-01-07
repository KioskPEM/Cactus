<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "bootstrap.php";

const APP_URL = "https://github.com/TheWhoosher/Cactus/archive/master.zip";

function release()
{
    global $zipArchive;
    if (isset($zipArchive)) {
        $zipArchive->close();
    }

    global $curlHandler;
    if (isset($curlHandler)) {
        curl_close($curlHandler);
    }

    global $archivePath;
    if (file_exists($archivePath)) {
        unlink($archivePath);
    }
}

function report(int $code, string $message)
{
    release();

    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        "message" => $message
    ]);
    exit($code === 200 ? 0 : 1);
}

function recursive_rmdir($dir): bool
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object) && !is_link($dir . "/" . $object))
                    recursive_rmdir($dir . "/" . $object);
                else if (!unlink($dir . "/" . $object))
                    return false;
            }
        }
        if (!rmdir($dir))
            return false;
    }
    return true;
}

/** @var string $tmpDir */
$tmpDir = sys_get_temp_dir();

/** @var string $archivePath */
$archivePath = tempnam($tmpDir, "Cactus");

/** @var false|resource $ch */
$curlHandler = curl_init();
curl_setopt_array($curlHandler, [
    CURLOPT_URL => APP_URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_BINARYTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT => "Cactus",
    CURLOPT_CONNECTTIMEOUT => 60,
    CURLOPT_TIMEOUT => 60
]);

/** @var bool|string $downloadData */
$downloadData = curl_exec($curlHandler);
if (!$downloadData) {
    $error = curl_error($curlHandler);
    report(500, "Unable to download the latest version of Cactus: " . $error);
}

if (!file_put_contents($archivePath, $downloadData)) {
    report(500, "Unable to save the archive to the disk.");
}

/** @var ZipArchive $zipArchive */
$zipArchive = new ZipArchive();
if (!$zipArchive->open($archivePath)) {
    report(500, "Unable to open the zip archive. The download is probably corrupted.");
}

if (!$zipArchive->extractTo($tmpDir)) {
    report(500, "Unable to extract the zip archive. It's probably permission-related?");
}

/** @var string $extractedFolder */
$extractedFolder = $tmpDir . DIRECTORY_SEPARATOR . "Cactus-master";
/** @var string $destination */
$destination = dirname(ROOT);

if (!rename($extractedFolder, ROOT)) {
    report(500, "Unable to move the up-to-date application to the Web directory.");
}

recursive_rmdir($extractedFolder);
report(200, "Cactus is now up-to-date");