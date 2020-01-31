<?php


namespace Cactus\User;


class UserManager
{
    public function createUser(string $firstName, string $lastName, string $schoolId)
    {
        return new User(0, $firstName, $lastName, $schoolId);
    }
}