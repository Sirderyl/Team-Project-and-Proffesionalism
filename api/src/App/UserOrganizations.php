<?php

namespace App;

/**
 * Implementation of /user/{id}/organizations endpoint
 */
class UserOrganizations
{
    private Database\DatabaseInterface $database;
    public function __construct(Database\DatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function execute(int $userId)
    {
        return $this->database->users()->getOrganizations($userId);
    }
}
