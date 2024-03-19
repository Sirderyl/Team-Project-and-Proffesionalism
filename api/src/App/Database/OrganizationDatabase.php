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

    public function get(int $id): \App\Organization {
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
            ':admin_id' => $organization->adminId
        ]);

        $organization->id = $this->connection->lastInsertId();
    }

    public function setUserStatus(int $organizationId, int $userId, \App\UserOrganizationStatus $status): void {
        // No affiliation is represented as the lack of a row
        if ($status == \App\UserOrganizationStatus::None) {
            $this->connection->execute(
                'DELETE FROM user_organization WHERE user_id = :userId AND organization_id = :organizationId',
                ['userId' => $userId, 'organizationId' => $organizationId]
            );
        } else {
            $this->connection->execute(
                'INSERT OR REPLACE INTO user_organization (user_id, organization_id, status) VALUES (:userId, :organizationId, :status)',
                ['userId' => $userId, 'organizationId' => $organizationId, 'status' => $status->value]
            );
        }
    }
}
