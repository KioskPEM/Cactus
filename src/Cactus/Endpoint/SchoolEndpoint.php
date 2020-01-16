<?php


namespace Cactus\Endpoint;


use Cactus\Http\HttpCode;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;

class SchoolEndpoint implements IRouteEndpoint
{
    private const REGION_CODE = 12;
    private const DEPARTMENT_CODE = 10;

    /**
     * @inheritDoc
     */
    public function handle(Route $route, array $parameters): string
    {
        $handle = fopen(ASSET_PATH . "schools.csv", "r");
        if ($handle === false)
            return $this->error();

        $header = fgetcsv($handle, 0, ';');
        if ($header === false) {
            fclose($handle);
            return $this->error();
        }

        $regionCode = $parameters["region_code"] ?? "00";
        $departmentCode = $parameters["department_code"] ?? "00";

        $database = [];
        while (($data = fgetcsv($handle, 0, ';')) !== false) {

            // filter region
            if ($regionCode !== "00" && $data[self::REGION_CODE] != $regionCode)
                continue;

            // filter department
            if ($departmentCode !== "00" && $data[self::DEPARTMENT_CODE] != $departmentCode)
                continue;

            $database[] = $data;
        }

        fclose($handle);
        return $this->formatOutput(HttpCode::SUCCESS_OK, $database);
    }

    private function error(): string
    {
        return $this->formatOutput(HttpCode::SERVER_ERROR, ["message" => "Server error"]);
    }

    private function formatOutput(int $code, array $data = []): string
    {
        http_response_code($code);
        header('Content-Type: application/json');
        return json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }
}