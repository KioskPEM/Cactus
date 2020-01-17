<?php


namespace Cactus\Controller\SignUp;


use Cactus\Database\CsvDatabase;
use Cactus\Template\Controller\ITemplateController;
use Cactus\Template\Render\RenderContext;

class SelectDepartmentController implements ITemplateController
{
    private const DEPARTMENT_CODE = 0;
    private const DEPARTMENT_NAME = 1;
    private const REGION_CODE = 2;

    public function get_departments(RenderContext $context): string
    {
        $regionCode = $context->param("route.region_code");

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

            $url = "http://127.0.0.1:8080/public/index.php?lang=fr&path=sign-up/region/$regionCode/department/$departmentCode";
            $output .= "<li class=\"grid-list-item\"><a class=\"button\" href=\"$url\">$departmentName</a></li>";
        }
        return $output;
    }
}