<?php

namespace App\Database;

/**
 * Implementation of OrganizationDatabaseInterface
 */
class OrganizationDatabase implements OrganizationDatabaseInterface {
    private readonly ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection) {
        $this->connection = $connection;
    }

    public function get(string $id): \App\Organization {
        $result = $this->connection->execute('
            SELECT id, name, admin_id
            FROM organizations
            WHERE id = :id
        ', [':id' => $id]);

        return \App\Organization::fromRow($result);
    }
}
