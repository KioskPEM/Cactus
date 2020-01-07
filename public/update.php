<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "bootstrap.php";

const APP_URL = "https://github.com/TheWhoosher/Cactus/archive/master.zip";

function release()
{
    global $extractedFolder;
    if (file_exists($extractedFolder))
        recursive_rmdir($extractedFolder);

    global $zipArchive;
    if (isset($zipArchive))
        $zipArchive->close();

    global $curlHandler;
    if (isset($curlHandler))
        curl_close($curlHandler);

    global $archivePath;
    if (file_exists($archivePath))
        unlink($archivePath);
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
    if (is_file($dir))
        return unlink($dir);

    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..")
                recursive_rmdir("$dir/$file");
        }
        rmdir($dir);
    }

    return true;
}

function recursive_copy($src, $dst)
{
    if (file_exists($dst))
        recursive_rmdir($dst);

    if (is_dir($src)) {
        mkdir($dst);
        $files = scandir($src);
        foreach ($files as $file) {
            if ($file != "." && $file != "..")
                recursive_copy("$src/$file", "$dst/$file");
        }
    } else if (file_exists($src))
        copy($src, $dst);
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
$extractedFolder = $tmpDir . DIRECTORY_SEPARATOR . "Cactus-master" . DIRECTORY_SEPARATOR;

recursive_copy($extractedFolder . "assets", ROOT . "assets");
recursive_copy($extractedFolder . "public", ROOT . "public");
recursive_copy($extractedFolder . "src", ROOT . "src");
recursive_copy($extractedFolder . "static", ROOT . "static");

report(200, "Cactus is now up-to-date");