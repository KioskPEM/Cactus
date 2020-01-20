<?php


namespace Cactus\Controller\SearchSchool;


use Cactus\Database\CsvDatabase;
use Cactus\Template\Controller\ITemplateController;
use Cactus\Template\Render\RenderContext;

class SelectSchoolController implements ITemplateController
{
    private const SCHOOL_ID = 0;
    private const SCHOOL_NAME = 1;
    private const DEPARTMENT_CODE = 10;
    private const REGION_CODE = 12;

    public function get_schools(RenderContext $context): string
    {
        $regionCode = $context->param("route.region");
        $departmentCode = $context->param("route.department");
        $schoolType = $context->param("route.school_type");

        $schoolDatabase = new CsvDatabase($schoolType, ';');
        $schoolDatabase->open();
        $schools = $schoolDatabase->get(function ($entry) use ($departmentCode, $regionCode) {
            return $entry[self::REGION_CODE] === $regionCode && $entry[self::DEPARTMENT_CODE] === $departmentCode;
        });
        $schoolDatabase->close();

        $output = "";
        foreach ($schools as $school) {
            $schoolId = $school[self::SCHOOL_ID];
            $url = "http://127.0.0.1:8080/public/index.php?lang=fr&path=sign-up/" . $schoolId;
            $schoolName = $school[self::SCHOOL_NAME];
            $output .= "<li class=\"grid-list-item\"><a class=\"button\" href=\"$url\">$schoolName</a></li>";
        }
        return $output;
    }
}