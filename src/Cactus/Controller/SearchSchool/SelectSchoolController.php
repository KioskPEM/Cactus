<?php


namespace Cactus\Controller\SearchSchool;


use Cactus\Database\CsvDatabase;
use Cactus\Routing\Exception\RouteNotFoundException;
use Cactus\Template\Controller\ITemplateController;
use Cactus\Template\Render\RenderContext;

class SelectSchoolController implements ITemplateController
{
    private const SCHOOL_ID = "Identifiant_de_l_etablissement";
    private const SCHOOL_NAME = "Nom_etablissement";
    private const DEPARTMENT_CODE = "Code_departement";
    private const REGION_CODE = "Code_region";

    private const SCHOOL_PER_PAGES = 14;

    private array $schools;

    /**
     * @inheritDoc
     */
    function onRender(RenderContext $context): void
    {
        $schoolType = $context->param("route.school_type");
        $schoolDatabase = new CsvDatabase($schoolType, ';');
        $schoolDatabase->open();

        $regionCode = $context->param("route.region");
        $departmentCode = $context->param("route.department");
        $this->schools = $schoolDatabase->get(function ($entry) use ($regionCode, $departmentCode) {
            return $entry[self::REGION_CODE] === $regionCode && $entry[self::DEPARTMENT_CODE] === $departmentCode;
        });
        $schoolDatabase->close();

        usort($this->schools, function ($a, $b) {
            return $a[self::SCHOOL_NAME] <=> $b[self::SCHOOL_NAME];
        });
    }

    /**
     * @param RenderContext $context
     * @return string
     * @throws RouteNotFoundException
     */
    public function get_schools(RenderContext $context): string
    {
        $output = "";

        $page = intval($context->param("route.page"));
        $offset = $page * self::SCHOOL_PER_PAGES;
        $schoolCount = count($this->schools);
        for ($i = 0; $i < self::SCHOOL_PER_PAGES && (($offset + $i) < $schoolCount); $i++) {
            $school = $this->schools[$offset + $i];

            $url = $context->buildUrl("GET", "sign-up.register", [
                "school_id" => $school[self::SCHOOL_ID]
            ]);
            $schoolName = $school[self::SCHOOL_NAME];
            $output .= "<a class=\"btn btn-expand\" href=\"$url\">$schoolName</a>";
        }

        return $output;
    }

    /**
     * @param RenderContext $context
     * @return string
     * @throws RouteNotFoundException
     */
    public function get_pagination(RenderContext $context): string
    {
        $output = "";

        $schoolCount = count($this->schools);

        $regionCode = $context->param("route.region");
        $departmentCode = $context->param("route.department");
        $schoolType = $context->param("route.school_type");

        $selectedPage = $context->param("route.page");
        $pageCount = ceil($schoolCount / self::SCHOOL_PER_PAGES);
        for ($i = 0; $i < $pageCount; $i++) {

            $page = $i + 1;
            $url = $context->buildUrl("GET", "search-school.school", [
                "region" => $regionCode,
                "department" => $departmentCode,
                "school_type" => $schoolType,
                "page" => $i
            ]);

            if ($selectedPage == $i)
                $output .= "<li class='pagination-list-item'><a class=\"btn selected\" href=\"$url\">$page</a></li>";
            else
                $output .= "<li class='pagination-list-item'><a class=\"btn\" href=\"$url\">$page</a></li>";
        }

        return $output;
    }
}