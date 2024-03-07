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
        $result = $this->connection->query('
            SELECT id, name, admin_id
            FROM organization
            WHERE id = :id
        ', [':id' => $id]);

        return \App\Organization::fromRow($result[0]);
    }

    public function create(\App\Organization $organization): void {
        $this->connection->execute('
            INSERT INTO organization (name, admin_id)
            VALUES (:name, :admin_id)
        ', [
            ':name' => $organization->name,
            ':admin_id' => $organization->admin_id
        ]);

        $organization->id = $this->connection->lastInsertId();
    }
}
