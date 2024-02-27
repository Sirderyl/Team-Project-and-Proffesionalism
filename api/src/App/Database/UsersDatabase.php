<?php

namespace App\Database;

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
            WHERE email = :email COLLATE NOCASE
            LEFT JOIN user_availability ON user.id = user_availability.user_id',
            ['email' => $email]
        );

        if (empty($result)) {
            throw new NotFoundException();
        }

        $user = new \App\User();
        $user->userId = $result['id'];
        $user->userName = $result['name'];
        // TODO
        // $user->email
        $user->phoneNumber = $result['phone_number'];

        // FIXME: The setter has 14 parameters! This is a code smell.
        // Just pass it manually for now.
        $availability = [];
        foreach ($result as $row) {
            if ($row['day_of_week'] === null) {
                continue;
            }

            // FIXME: startTime and endTime should be startTime and endTime.
            $availability[$row['day_of_week']] = [
                'startTime' => $row['start_hour'],
                'endTime' => $row['end_hour'],
            ];
        }

        $user->availability = $availability;
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
                // TODO
                'email' => $user->userName . '@example.com',
                // TODO
                'password_hash' => '12345',
                // 'email' => $data->
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
                    'start_hour' => $times['startTime'],
                    'end_hour' => $times['endTime'],
                ]
            );
        }
    }
}
