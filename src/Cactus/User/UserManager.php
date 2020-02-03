<?php


namespace Cactus\User;


use Cactus\Database\Database;
use Cactus\User\Exception\UserException;

class UserManager
{
    private function __construct()
    {
    }

    public static function Instance(): UserManager
    {
        static $inst = null;
        if ($inst === null)
            $inst = new UserManager();
        return $inst;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $schoolId
     * @return User
     * @throws UserException
     */
    public function createUser(string $firstName, string $lastName, string $schoolId)
    {
        $database = Database::Instance();
        $statement = $database->prepare("INSERT INTO `kiosk-pem`.users(first_name, last_name, school_id) VALUES(:first_name, :last_name, :school_id)");
        $statement->bindValue(":first_name", $firstName);
        $statement->bindValue(":last_name", $lastName);
        $statement->bindValue(":school_id", $schoolId);

        if (!$statement->execute())
            throw new UserException("Unable to create the user");

        $id = $database->lastInsertId();
        return new User($id, $firstName, $lastName, $schoolId);
    }
}