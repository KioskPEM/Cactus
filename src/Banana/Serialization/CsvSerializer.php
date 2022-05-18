<?php

namespace Banana\Serialization;

use Banana\IO\FileException;

class CsvSerializer
{
    private function __construct()
    {
    }

    /**
     * @param string $path
     * @param bool $hasHeader
     * @param string $separator
     * @param string $enclosure
     * @param string $escape
     * @return array
     * @throws FileException
     * @throws CsvException
     */
    public static function deserializeFile(
        string $path,
        bool   $hasHeader = true,
        string $separator = ",",
        string $enclosure = "\"",
        string $escape = "\\"
    ): array
    {
        $handle = fopen($path, "r");
        if ($handle === false)
            throw new FileException("Failed to open file");

        try {
            $header = [];

            if ($hasHeader) {
                $header = fgetcsv($handle, 0, $separator, $enclosure, $escape);
                if ($header === false)
                    throw new CsvException("Header is not present");
            }

            $entries = [];
            while (($entry = fgetcsv($handle, 0, $separator, $enclosure, $escape)) !== false) {

                if ($hasHeader) {
                    foreach ($header as $index => $name) {
                        $entry[$name] = $entry[$index];
                    }
                }

                $entries[] = $entry;
            }

            return $entries;

        } finally {
            fclose($handle);
        }
    }
}