<?php


namespace Cactus\Controller\SearchSchool;


use Cactus\Database\CsvDatabase;
use Cactus\Routing\Exception\RouteNotFoundException;
use Cactus\Template\Controller\ITemplateController;
use Cactus\Template\Render\RenderContext;

class SelectDepartmentController implements ITemplateController
{
    private const DEPARTMENT_CODE = "code_departement";
    private const DEPARTMENT_NAME = "nom_departement";
    private const REGION_CODE = "code_region";

    /**
     * @param RenderContext $context
     * @return string
     * @throws RouteNotFoundException
     */
    public function get_departments(RenderContext $context): string
    {
        $regionCode = $context->param("route.region");

        $departmentDatabase = new CsvDatabase("departments");
        $departmentDatabase->open();
        $departments = $departmentDatabase->get(function ($entry) use ($regionCode) {
            return $entry[self::REGION_CODE] === $regionCode;
        });
        $departmentDatabase->close();

        $output = "";
        foreach ($departments as $department) {
            $departmentCode = $department[self::DEPARTMENT_CODE];
            $departmentName = $department[self::DEPARTMENT_NAME];

            $url = $context->buildUrl("GET", "search-school.school-type", [
                "region" => $regionCode,
                "department" => $departmentCode,
            ]);
            $output .= "<li class=\"grid-list-item\"><a class=\"button\" href=\"$url\">$departmentName</a></li>";
        }
        return $output;
    }
}