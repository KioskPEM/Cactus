<?php


namespace Cactus\User;


class User
{
    private string $uniqueId;
    private string $firstName;
    private string $lastName;
    private string $schoolId;

    private bool $internship;

    /**
     * User constructor.
     * @param string $uniqueId
     * @param string $firstName
     * @param string $lastName
     * @param string $schoolId
     */
    public function __construct(string $uniqueId, string $firstName, string $lastName, string $schoolId)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->schoolId = $schoolId;
        $this->uniqueId = $uniqueId;
    }

    public function isAskingForInternship(): bool
    {
        return $this->internship;
    }

    public function askForInternship(): void
    {
        $this->internship = true;
    }

    /**
     * @return string
     */
    public function getUniqueId(): string
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