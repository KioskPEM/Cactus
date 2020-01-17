<?php


namespace Cactus\Schools;


class School
{
    private string $id;
    private string $name;
    private string $department;

    /**
     * School constructor.
     * @param string $id
     * @param string $name
     * @param string $department
     */
    public function __construct(string $id, string $name, string $department)
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

    public function getDepartment(): string
    {
        return $this->department;
    }
}