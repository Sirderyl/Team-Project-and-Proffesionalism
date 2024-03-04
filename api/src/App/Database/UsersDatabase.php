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

        $user = new \App\User();
        $firstRow = $result[0];
        $user->userId = $firstRow['id'];
        $user->userName = $firstRow['name'];
        $user->email = $firstRow['email'];
        $user->passwordHash = $firstRow['password_hash'];
        $user->phoneNumber = $firstRow['phone_number'];

        foreach ($result as $row) {
            /** @var string|null $day */
            $day = $row['day_of_week'];
            if ($day === null) {
                continue;
            }
            $user->setAvailability(\App\DayOfWeek::from($day), new TimeRange(
                $row['start_hour'],
                $row['end_hour']
            ));
        }
        return $user;
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

    public function getProfilePicture(string $userId): string
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

    public function setProfilePicture(string $userId, string|null $data): void
    {
        $this->connection->execute(
            'UPDATE user SET profile_picture = :data WHERE id = :id',
            ['data' => $data, 'id' => $userId]
        );
    }
}
