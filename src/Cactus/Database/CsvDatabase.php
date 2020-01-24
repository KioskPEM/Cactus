<?php


namespace Cactus\Database;


class CsvDatabase
{
    private string $name;
    private string $delimiter;
    private array $header;

    /**
     * @var resource
     */
    private $handle;

    /**
     * CsvDatabase constructor.
     * @param string $name
     * @param string $delimiter
     */
    public function __construct(string $name, string $delimiter = ',')
    {
        $this->name = $name;
        $this->delimiter = $delimiter;
    }

    public function get(callable $filter = null): array
    {
        $replaceKeys = isset($this->header);

        $results = [];
        while (($entry = fgetcsv($this->handle, 0, $this->delimiter)) !== false) {

            if ($replaceKeys) {
                foreach ($this->header as $key => $name) {
                    $entry[$name] = $entry[$key];
                    unset($entry[$key]);
                }
            }

            if ($filter && $filter($entry) == false)
                continue;

            $results[] = $entry;
        }

        return $results;
    }

    public function open(bool $useHeader = true): bool
    {
        $this->handle = fopen(DATA_PATH . $this->name . ".csv", "r");
        if ($this->handle === false)
            return false;

        if ($useHeader) {
            $this->header = fgetcsv($this->handle, 0, $this->delimiter);
            if ($this->header === false) {
                $this->close();
                return false;
            }
        }

        return true;
    }

    public function close(): bool
    {
        return fclose($this->handle);
    }

}