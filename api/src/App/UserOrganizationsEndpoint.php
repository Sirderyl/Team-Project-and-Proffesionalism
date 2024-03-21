<?php

namespace App;

/**
 * Implementation of /user/{id}/organizations endpoint
 */
class UserOrganizationsEndpoint
{
    private Database\DatabaseInterface $database;
    public function __construct(Database\DatabaseInterface $database)
    {
        $this->database = $database;
    }

    /**
     * @return array<array{
     *   id: int,
     *   name: string,
     *   status: string,
     * }>
     */
    public function execute(int $userId): array
    {
        return $this->database->users()->getOrganizations($userId);
    }
}
