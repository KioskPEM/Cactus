<?php


namespace Cactus\User;


use Cactus\User\Exception\UserException;
use PDO;

class UserManager
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $schoolId
     * @return User
     * @throws UserException
     */
    public function createUser(string $firstName, string $lastName, string $schoolId): User
    {
        $statement = $this->pdo->prepare("INSERT INTO `kiosk-pem`.users(first_name, last_name, school_id) VALUES(:first_name, :last_name, :school_id)");
        $statement->bindValue(":first_name", $firstName);
        $statement->bindValue(":last_name", $lastName);
        $statement->bindValue(":school_id", $schoolId);

        if (!$statement->execute())
            throw new UserException("Unable to create the user");

        $id = $this->pdo->lastInsertId();
        return new User($id, $firstName, $lastName, $schoolId);
    }

    /**
     * @param string $userId
     * @return User
     * @throws UserException
     */
    public function loginUser(int $userId): User
    {
        $statement = $this->pdo->prepare("SELECT * FROM `kiosk-pem`.users WHERE users.id = :user_id");
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

    /**
     * @param User $user
     * @return void
     * @throws UserException
     */
    public function updateUser(User $user)
    {
        $statement = $this->pdo->prepare("UPDATE users SET internship = :internship where id = :user_id");
        $statement->bindValue(":user_id", $user->getUniqueId(), PDO::PARAM_INT);
        $statement->bindValue(":internship", $user->isAskingForInternship(), PDO::PARAM_BOOL);

        if (!$statement->execute())
            throw new UserException("Unable to update user " . $user->getUniqueId());
    }
}