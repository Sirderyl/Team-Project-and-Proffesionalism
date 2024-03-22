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
        // TODO: Support multiple admins
        $result = $this->connection->query("
            SELECT
                id,
                name,
                (SELECT user_id FROM user_organization WHERE organization_id = organization.id AND status = 'Manager') AS admin_id
            FROM organization
            WHERE id = :id
        ", [':id' => $id]);

        return \App\Organization::fromRow($result[0]);
    }

    public function create(\App\Organization $organization): void {
        $this->connection->execute('
            INSERT INTO organization (name)
            VALUES (:name)
        ', [
            ':name' => $organization->name,
        ]);

        $organization->id = $this->connection->lastInsertId();

        $this->connection->execute("
            INSERT INTO user_organization (user_id, organization_id, status)
            VALUES (:user_id, :organization_id, 'Manager')
        ", [ ':user_id' => $organization->adminId, ':organization_id' => $organization->id ]);
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

    public function getUserStatus(int $organizationId, int $userId): \App\UserOrganizationStatus {
        $result = $this->connection->query(
            'SELECT status FROM user_organization WHERE user_id = :userId AND organization_id = :organizationId',
            ['userId' => $userId, 'organizationId' => $organizationId]
        );

        // No affiliation is represented as the lack of a row
        if (empty($result)) {
            return \App\UserOrganizationStatus::None;
        }

        return \App\UserOrganizationStatus::from($result[0]['status']);
    }

    public function getActivitySchedule(int $organizationId, \DateTime $from = null, \DateTime $to = null): array {
        $result = $this->connection->query(
            "SELECT
                activity.id AS activity_id,
                activity.name AS activity_name,
                activity.short_description AS activity_short_description,
                user_activity.start_time AS start_time,
                json_group_array(json_object(
                    'id', user.id,
                    'name', user.name
                )) AS users
                FROM activity
                LEFT JOIN user_activity ON activity.id = user_activity.activity_id
                LEFT JOIN user ON user_activity.user_id = user.id
                WHERE activity.organization_id = :organizationId
                AND (:from IS NULL OR user_activity.start_time >= :from)
                AND (:to IS NULL OR user_activity.start_time <= :to)
                GROUP BY activity.id, user_activity.start_time",
                [
                    'organizationId' => $organizationId,
                    'from' => $from ? $from->format(\DateTime::ISO8601) : null,
                    'to' => $to ? $to->format(\DateTime::ISO8601) : null,
                ]
            );

        return array_map(fn ($row) => [
            'activity' => [
                'id' => $row['activity_id'],
                'name' => $row['activity_name'],
                'shortDescription' => $row['activity_short_description'],
            ],
            'startTime' => new \DateTime($row['start_time']),
            'users' => json_decode($row['users'], true),
        ], $result);
    }
}
