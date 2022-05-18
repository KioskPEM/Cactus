<?php


namespace Cactus\Controller\SearchSchool;


use Banana\IO\FileException;
use Banana\Routing\RouteFormatException;
use Banana\Routing\RouteNotFoundException;
use Banana\Serialization\CsvException;
use Banana\Serialization\CsvSerializer;
use Banana\Template\Render\IRenderHandler;
use Banana\Template\Render\RenderContext;

class SelectDepartmentController implements IRenderHandler
{
    private const DEPARTMENT_CODE = "code_departement";
    private const DEPARTMENT_NAME = "nom_departement";
    private const REGION_CODE = "code_region";

    /**
     * @inheritDoc
     */
    function onRender(RenderContext $context): void
    {
    }

    /**
     * @param RenderContext $context
     * @return string
     * @throws CsvException
     * @throws FileException
     * @throws RouteNotFoundException
     * @throws RouteFormatException
     */
    public function get_departments(RenderContext $context): string
    {
        $regionCode = $context->param("route.region");

        $departments = CsvSerializer::deserializeFile(DATA_PATH . "departments.csv");
        $departments = array_filter($departments, function ($entry) use ($regionCode) {
            return $entry[self::REGION_CODE] === $regionCode;
        });

        $output = "";
        foreach ($departments as $department) {
            $departmentCode = $department[self::DEPARTMENT_CODE];
            $departmentName = $department[self::DEPARTMENT_NAME];

            $router = $context->getRouter();
            $url = $router->buildUrl("GET", "school.type", [
                "region" => $regionCode,
                "department" => $departmentCode,
            ]);
            $output .= "<a class=\"btn btn-large margin-5px\" href=\"$url\">$departmentName</a>";
        }
        return $output;
    }
}