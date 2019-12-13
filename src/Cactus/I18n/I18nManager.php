<?php

namespace Cactus\I18n;


use Cactus\I18n\Exception\LanguageNotFoundException;

class I18nManager
{
    private array $languages = [];

    private function resolvePath(string $lang): string
    {
        return ASSET_PATH . "i18n" . DIRECTORY_SEPARATOR . $lang . ".json";
    }

    /**
     * @param string $lang
     * @return array
     * @throws LanguageNotFoundException
     */
    public function load(string $lang): array
    {
        if (array_key_exists($lang, $this->languages))
            return $this->languages[$lang];

        $path = $this->resolvePath($lang);
        if (!file_exists($path))
            throw new LanguageNotFoundException("Unknown language " . $lang);

        $content = file_get_contents($path);
        return $this->languages[$lang] = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

}