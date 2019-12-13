<?php


namespace Cactus\Template\Render;

use Cactus\Util\ArrayPath;

class RenderContext
{
    private array $i18n;
    private array $params;

    /**
     * RenderContext constructor.
     * @param array $i18n
     * @param array $params
     */
    public function __construct(array $i18n, array $params = [])
    {
        $this->i18n = $i18n;
        $this->params = $params;
    }

    public function hasParam(string $key): bool
    {
        return ArrayPath::has($this->params, $key);
    }

    public function param(string $key): string
    {
        return ArrayPath::get($this->params, $key);
    }

    public function append(array $other): void
    {
        $this->params = array_replace_recursive($this->params, $other);
    }

    public function translate(string $key): string
    {
        $value = ArrayPath::get($this->i18n, $key);

        if (!$value)
            return $key;

        if (is_array($value)) {
            $temp = '';
            foreach ($value as $entry)
                $temp .= $entry . '<br>';
            $value = $temp;
        }

        return $value;
    }
}