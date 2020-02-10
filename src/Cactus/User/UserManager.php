<?php


namespace Cactus\User;


use Cactus\Database\Database;
use Cactus\User\Exception\UserException;
use PDO;

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

    /**
     * @param string $userId
     * @return User
     * @throws UserException
     */
    public function loginUser(string $userId)
    {
        $database = Database::Instance();
        $statement = $database->prepare("SELECT * FROM `kiosk-pem`.users WHERE id = :user_id");
        $statement->bindValue(":user_id", $userId, PDO::PARAM_INT);

        if (!$statement->execute())
            throw new UserException("Unable to get the user with id " . $userId);

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false)
            throw new UserException("Unable to get the user data");

        return new User(
            $result["id"],
            $result["first_name"],
            $result["last_name"],
            $result["school_id"]
        );
    }
}