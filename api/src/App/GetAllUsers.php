<?php

namespace App;

/**
 * @author Nihal Kejman
 */
class GetAllUsers
{
    //
    private Database\DatabaseInterface $database;

    public function __construct(Database\DatabaseInterface $database)
    {
        $this->database = $database;
    }

    /** @return User[] */
    public function getAllUsers(): array
    {
        //getting all users
        return $this->database->users()->getAll();
    }

}
