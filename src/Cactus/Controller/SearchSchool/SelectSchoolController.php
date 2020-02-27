<?php


namespace Cactus\Controller\SearchSchool;


use Cactus\Database\CsvDatabase;
use Cactus\Routing\Exception\RouteNotFoundException;
use Cactus\Template\Controller\ITemplateController;
use Cactus\Template\Exception\TemplateException;
use Cactus\Template\Pagination;
use Cactus\Template\Render\RenderContext;

class SelectSchoolController implements ITemplateController
{
    private const SCHOOL_ID = "Identifiant_de_l_etablissement";
    private const SCHOOL_NAME = "Nom_etablissement";
    private const DEPARTMENT_CODE = "Code_departement";
    private const REGION_CODE = "Code_region";

    private Pagination $schools;

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
        $schoolEntries = $schoolDatabase->get(function ($entry) use ($regionCode, $departmentCode) {
            return $entry[self::REGION_CODE] === $regionCode && $entry[self::DEPARTMENT_CODE] === $departmentCode;
        });
        $schoolDatabase->close();

        usort($schoolEntries, function ($a, $b) {
            return $a[self::SCHOOL_NAME] <=> $b[self::SCHOOL_NAME];
        });

        $this->schools = new Pagination($schoolEntries);
    }

    /**
     * @param RenderContext $context
     * @return string
     * @throws RouteNotFoundException
     * @throws TemplateException
     */
    public function get_schools(RenderContext $context): string
    {
        $output = "";

        $page = intval($context->param("route.page"));
        $schools = $this->schools->getPage($page);

        $schoolIndex = 0;
        foreach ($schools as $school) {

            if($schoolIndex % 2 == 0) {
                if($schoolIndex > 0)
                    $output .= "</div>";
                $output .= "<div class='grid-row'>";
            }

            $url = $context->buildUrl("GET", "sign-up.register", [
                "school_id" => $school[self::SCHOOL_ID]
            ]);
            $schoolName = $school[self::SCHOOL_NAME];
            $output .= "<a class=\"btn btn-large margin-5px\" href=\"$url\">$schoolName</a>";


            $schoolIndex++;
        }
        $output .= "</div>";

        return $output;
    }

    /**
     * @param RenderContext $context
     * @return string
     * @throws RouteNotFoundException
     */
    public function get_pagination(RenderContext $context): string
    {
        $currentPage = intval($context->param("route.page"));

        $regionCode = $context->param("route.region");
        $departmentCode = $context->param("route.department");
        $schoolType = $context->param("route.school_type");

        $hasPreviousPage = $currentPage > 1;
        $hasNextPage = $currentPage < $this->schools->getPageCount();;

        if ($hasPreviousPage) {
            $previousPage = $context->buildUrl("GET", "search-school.school", [
                "region" => $regionCode,
                "department" => $departmentCode,
                "school_type" => $schoolType,
                "page" => $currentPage - 1
            ]);
        } else
            $previousPage = "#";

        if ($hasNextPage) {
            $nextPage = $context->buildUrl("GET", "search-school.school", [
                "region" => $regionCode,
                "department" => $departmentCode,
                "school_type" => $schoolType,
                "page" => $currentPage + 1
            ]);
        } else
            $nextPage = "#";


        $output = "";
        $output .= "<a class=\"btn\" href=\"$previousPage\"><i class='mdi mdi-arrow-left mdi-size-medium'></i></a>";
        $output .= "<a class=\"btn\" href=\"$nextPage\"><i class='mdi mdi mdi-arrow-right mdi-size-medium'></i></a>";
        return $output;
    }
}