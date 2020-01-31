<?php


namespace Cactus\User;


class User
{
    private int $uniqueId;
    private string $firstName;
    private string $lastName;
    private string $schoolId;

    /**
     * User constructor.
     * @param int $uniqueId
     * @param string $firstName
     * @param string $lastName
     * @param string $schoolId
     */
    public function __construct(int $uniqueId, string $firstName, string $lastName, string $schoolId)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->schoolId = $schoolId;
        $this->uniqueId = $uniqueId;
    }

    /**
     * @return int
     */
    public function getUniqueId(): int
    {
        return $this->uniqueId;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getSchoolId(): string
    {
        return $this->schoolId;
    }

}