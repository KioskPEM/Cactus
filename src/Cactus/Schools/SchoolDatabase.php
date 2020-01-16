<?php


namespace Cactus\Schools;


class SchoolDatabase
{
    private const REGION_CODE = 12;
    private const DEPARTMENT_CODE = 10;

    private function __construct()
    {
    }

    /**
     * @param int $regionCode
     * @param int $departmentCode
     * @return array|bool
     */
    public static function search(int $regionCode = 0, int $departmentCode = 0)
    {
        $handle = fopen(ASSET_PATH . "schools.csv", "r");
        if ($handle === false)
            return false;

        $header = fgetcsv($handle, 0, ';');
        if ($header === false) {
            fclose($handle);
            return false;
        }

        $results = [];
        while (($entry = fgetcsv($handle, 0, ';')) !== false) {

            // filter region
            if ($regionCode !== 0 && $entry[self::REGION_CODE] != $regionCode)
                continue;

            // filter department
            if ($departmentCode !== 0 && $entry[self::DEPARTMENT_CODE] != $departmentCode)
                continue;

            $results[] = new School(
                $entry[0],
                $entry[1],
                $entry[self::DEPARTMENT_CODE]
            );
        }

        fclose($handle);
        return $results;
    }
}