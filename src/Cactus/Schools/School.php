<?php


namespace Cactus\Schools;


class School
{
    public string $id;
    public string $name;
    public int $department;

    /**
     * School constructor.
     * @param string $id
     * @param string $name
     * @param int $department
     */
    public function __construct(string $id, string $name, int $department)
    {
        $this->id = $id;
        $this->name = $name;
        $this->department = $department;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDepartment(): int
    {
        return $this->department;
    }
}