<?php

namespace Banana\Localization;

use Banana\Collection\ArrayPath;
use Banana\IO\FileException;
use JsonException;

class LocalizationManager
{
    private array $languages = [];

    private function resolvePath(string $lang): string
    {
        return ASSET_PATH . "i18n" . DIRECTORY_SEPARATOR . $lang . ".json";
    }

    /**
     * @param string $lang
     * @return array
     * @throws FileException|JsonException
     */
    public function load(string $lang): array
    {
        if (array_key_exists($lang, $this->languages))
            return $this->languages[$lang];

        $path = $this->resolvePath($lang);
        if (!file_exists($path))
            throw new FileException("Locale file not found for " . $lang);

        $content = file_get_contents($path);
        return $this->languages[$lang] = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    public function translate(string $lang, string $key, string $lineDelimiter = "<br>")
    {
        try {
            $data = $this->load($lang);
        } catch (FileException|JsonException $e) {
            return $key;
        }

        $value = ArrayPath::get($data, $key);

        if (!$value)
            return $key;

        if (is_array($value)) {
            if (count($value) == 1)
                return $value[0];

            $temp = '';
            foreach ($value as $entry)
                $temp .= $entry . $lineDelimiter;
            $value = $temp;
        }

        return $value;
    }
}