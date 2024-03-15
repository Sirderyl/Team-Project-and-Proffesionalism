<?php

namespace App\Database;
use App\TimeRange;

/**
 * Implementation of UsersDatabaseInterface
 * @author Kieran
 */
class UsersDatabase implements UsersDatabaseInterface
{
    private readonly ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function get(string $email): \App\User
    {
        $result = $this->connection->query(
            'SELECT
                user.id, user.name, user.email, user.password_hash, user.phone_number,
                user_availability.day_of_week, user_availability.start_hour, user_availability.end_hour
            FROM user
            LEFT JOIN user_availability ON user.id = user_availability.user_id
            WHERE email = :email COLLATE NOCASE',
            ['email' => $email]
        );

        if (empty($result)) {
            throw new NotFoundException();
        }

        return \App\User::fromRows($result)[0];
    }

    public function create(\App\User $user): void
    {
        $this->connection->execute(
            'INSERT INTO user (
                name,
                email,
                password_hash,
                phone_number
            ) VALUES (
                :name,
                :email,
                :password_hash,
                :phone_number
            )',
            [
                'name' => $user->userName,
                'email' => $user->email,
                'password_hash' => $user->passwordHash,
                'phone_number' => $user->phoneNumber,
            ]
        );

        $user->userId = $this->connection->lastInsertId();

        foreach ($user->availability as $day => $times) {
            $this->connection->execute(
                'INSERT INTO user_availability (user_id, day_of_week, start_hour, end_hour) VALUES (:user_id, :day_of_week, :start_hour, :end_hour)',
                [
                    'user_id' => $user->userId,
                    'day_of_week' => $day,
                    'start_hour' => $times->start,
                    'end_hour' => $times->end
                ]
            );
        }
    }

    public function getProfilePicture(int $userId): ?string
    {
        $result = $this->connection->query(
            'SELECT profile_picture FROM user WHERE id = :id',
            ['id' => $userId]
        );

        if (empty($result)) {
            throw new NotFoundException();
        }

        return $result[0]['profile_picture'];
    }

    public function setProfilePicture(int $userId, string|null $data): void
    {
        $this->connection->execute(
            'UPDATE user SET profile_picture = :data WHERE id = :id',
            ['data' => $data, 'id' => $userId]
        );
    }

    public function getInvites(int $userId): array
    {
        $result = $this->connection->query("
            SELECT organization.id, organization.name, organization.admin_id
            FROM user_organization
            JOIN organization ON user_organization.organization_id = organization.id
            WHERE user_organization.user_id = :userId AND user_organization.status = 'Invited'
        ", ['userId' => $userId]);

        return array_map(fn ($row) => \App\Organization::fromRow($row), $result);
    }

    public function getAssignedActivities(int $userId): array
    {
        $result = $this->connection->query("
            SELECT
                activity.name, activity.id, activity.short_description,
                user_activity.start_time
            FROM user_activity
            JOIN activity ON user_activity.activity_id = activity.id
            WHERE user_activity.user_id = :userId
        ", ['userId' => $userId]);

        return array_map(fn ($row) => [
            'activity' => [
                'name' => $row['name'],
                'id' => $row['id'],
                'shortDescription' => $row['short_description']
            ],
            'start' => new \DateTime($row['start_time'])
        ], $result);
    }
}
