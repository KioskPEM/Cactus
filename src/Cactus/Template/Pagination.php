<?php

namespace Cactus\Template;


use Cactus\Template\Exception\TemplateException;

class Pagination
{
    /**
     * @var array
     */
    private array $entries;

    private int $entryPerPage;

    /**
     * Pagination constructor.
     * @param array $entries
     * @param int $entryPerPage
     */
    public function __construct(array $entries, int $entryPerPage = 14)
    {
        $this->entries = $entries;
        $this->entryPerPage = $entryPerPage;
    }

    public function generatePagination(int $page, int $adjacentPages = 2): array
    {
        $pageCount = $this->getPageCount();
        $pages = [];

        $start = ($page <= $adjacentPages ? 1 : $page - $adjacentPages);
        $end = ($page > $pageCount - $adjacentPages ? $pageCount : $page + $adjacentPages);

        for ($i = $start; $i <= $end; $i++)
            $pages[] = $i;
        return $pages;
    }

    /**
     * Gets the content of the page number
     *
     * @param int $page
     * @return array
     * @throws TemplateException
     */
    public function getPage(int $page): array
    {
        if ($page < 1 || $page > $this->getPageCount())
            throw new TemplateException("Invalid page " . $page);
        $entryOffset = ($page - 1) * $this->entryPerPage;
        return array_slice($this->entries, $entryOffset, $this->entryPerPage);
    }

    /**
     * Gets the number of page for the current pagination
     *
     * @return false|float
     */
    public function getPageCount()
    {
        return ceil(count($this->entries) / $this->entryPerPage);
    }

    /**
     * @return int
     */
    public function getEntryPerPage(): int
    {
        return $this->entryPerPage;
    }

    /**
     * @param int $entryPerPage
     */
    public function setEntryPerPage(int $entryPerPage): void
    {
        $this->entryPerPage = $entryPerPage;
    }

}