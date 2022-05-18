<?php


namespace Cactus\Controller\SearchSchool;


use Banana\Collection\Pagination;
use Banana\IO\FileException;
use Banana\Routing\RouteFormatException;
use Banana\Routing\RouteNotFoundException;
use Banana\Serialization\CsvException;
use Banana\Serialization\CsvSerializer;
use Banana\Template\Render\IRenderHandler;
use Banana\Template\Render\RenderContext;
use Banana\Template\TemplateException;

class SelectSchoolController implements IRenderHandler
{
    private const SCHOOL_ID = "Identifiant_de_l_etablissement";
    private const SCHOOL_NAME = "Nom_etablissement";
    private const DEPARTMENT_CODE = "Code_departement";
    private const REGION_CODE = "Code_region";

    private Pagination $schools;

    /**
     * @inheritDoc
     * @throws FileException
     * @throws CsvException
     */
    function onRender(RenderContext $context): void
    {
        $schoolType = $context->param("route.school_type");
        $regionCode = $context->param("route.region");
        $departmentCode = $context->param("route.department");

        $schools = CsvSerializer::deserializeFile(DATA_PATH . $schoolType . ".csv", true, ';');
        $schools = array_filter($schools, function ($entry) use ($departmentCode, $regionCode) {
            return $entry[self::REGION_CODE] === $regionCode && $entry[self::DEPARTMENT_CODE] === $departmentCode;
        });

        usort($schools, function ($a, $b) {
            return $a[self::SCHOOL_NAME] <=> $b[self::SCHOOL_NAME];
        });

        $this->schools = new Pagination($schools, 14);
    }

    /**
     * @param RenderContext $context
     * @return string
     * @throws RouteNotFoundException
     * @throws TemplateException
     * @throws RouteFormatException
     */
    public function get_schools(RenderContext $context): string
    {
        $output = "";

        $page = intval($context->param("route.page"));
        $schools = $this->schools->getPage($page);

        $schoolIndex = 0;
        foreach ($schools as $school) {

            if ($schoolIndex % 2 == 0) {
                if ($schoolIndex > 0)
                    $output .= "</div>";
                $output .= "<div class='grid-row'>";
            }

            $router = $context->getRouter();
            $url = $router->buildUrl("GET", "register.form", [
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
     * @throws RouteFormatException
     */
    public function get_pagination(RenderContext $context): string
    {
        $currentPage = intval($context->param("route.page"));

        $regionCode = $context->param("route.region");
        $departmentCode = $context->param("route.department");
        $schoolType = $context->param("route.school_type");

        $pageCount = $this->schools->getPageCount();

        $hasPreviousPage = $currentPage > 1;
        $hasNextPage = $currentPage < $pageCount;

        $output = "";

        if ($hasPreviousPage) {
            $router = $context->getRouter();
            $previousPage = $router->buildUrl("GET", "school.institution", [
                "region" => $regionCode,
                "department" => $departmentCode,
                "school_type" => $schoolType,
                "page" => $currentPage - 1
            ]);
            $output .= "<a class=\"btn\" href=\"$previousPage\"><i class='mdi mdi-arrow-left mdi-size-medium'></i></a>";
        } else
            $output .= "<a class=\"btn invisible\" href=\"#\"><i class='mdi mdi-arrow-left mdi-size-medium'></i></a>";

        $output .= "<p>Page $currentPage / $pageCount</p>";

        if ($hasNextPage) {
            $router = $context->getRouter();
            $nextPage = $router->buildUrl("GET", "school.institution", [
                "region" => $regionCode,
                "department" => $departmentCode,
                "school_type" => $schoolType,
                "page" => $currentPage + 1
            ]);
            $output .= "<a class=\"btn\" href=\"$nextPage\"><i class='mdi mdi-arrow-right mdi-size-medium'></i></a>";
        } else
            $output .= "<a class=\"btn invisible\" href=\"#\"><i class='mdi mdi-arrow-right mdi-size-medium'></i></a>";

        return $output;
    }
}