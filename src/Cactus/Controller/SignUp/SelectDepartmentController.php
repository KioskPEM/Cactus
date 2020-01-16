<?php


namespace Cactus\Controller\SignUp;


use Cactus\Schools\School;
use Cactus\Schools\SchoolDatabase;
use Cactus\Template\Controller\ITemplateController;
use Cactus\Template\Render\RenderContext;

class SelectDepartmentController implements ITemplateController
{
    public function get_departments(RenderContext $context): string
    {
        $regionCode = $context->param("route.region_code");
        $schools = SchoolDatabase::search($regionCode);

        $output = "";
        $departments = [];

        foreach ($schools as $school) {
            /** @var School $school */

            $output .= "<li class=\"grid-list-item\"><a class=\"button\" href=\"@{error|error=404}\" style=\"background-color: red;\">Error 404</a></li>";
        }

        return $output;
    }
}