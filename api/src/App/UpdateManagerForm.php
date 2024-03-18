<?php

namespace App;

/**
 * @author Nihal Kejman 
 */
class UpdateManagerForm
{

    private Database\DatabaseInterface $database;

    public function __construct(Database\DatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function getUserStatus(int $organizationId, int $userId): string
    {
        //getting users status
        return $this->database->organizations()->getUserStatus($organizationId, $userId)->value;
    }

    public function setUserStatus(int $organizationId, int $userId, UserOrganizationStatus $status): void
    {
        // setting user status
        $this->database->organizations()->setUserStatus($organizationId, $userId, $status);
    }

}
