<?php


namespace Cactus\Database;


use Cactus\Util\AppConfiguration;
use PDO;
use PDOStatement;

class Database
{
    private function __construct()
    {
    }

    public static function Instance(): Database
    {
        static $inst = null;
        if ($inst === null)
            $inst = new Database();
        return $inst;
    }

    /**
     * @var PDO
     */
    private $pdo;

    public function prepare(string $sql): PDOStatement
    {
        $pdo = $this->ensureConnected();
        return $pdo->prepare($sql);
    }

    private function ensureConnected(): PDO
    {
        if (isset($this->pdo))
            return $this->pdo;

        $config = AppConfiguration::Instance();
        $host = $config->get("database.host");
        $name = $config->get("database.name");
        $user = $config->get("database.user");
        $pass = $config->get("database.pass");
        return $this->pdo = new PDO(
            "mysql:host=" . $host . ";dbname=" . $name,
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }

    public function lastInsertId(string $name = null)
    {
        $pdo = $this->ensureConnected();
        return $pdo->lastInsertId($name);
    }

}