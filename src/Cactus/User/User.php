<?php


namespace Cactus\User;


class User
{
    private string $uniqueId;
    private string $firstName;
    private string $lastName;
    private string $schoolId;

    private bool $placement;

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

    /**
     * @return bool
     */
    public function isAskingForPlacement(): bool
    {
        return $this->placement;
    }

    /**
     * @param bool $placement
     */
    public function setPlacement(bool $placement): void
    {
        $this->placement = $placement;
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