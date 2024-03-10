<?php

namespace App\Database;

/**
 * Implementation of AvailabilityDatabaseInterface
 * @author Filip
 */
class AvailabilityDatabase implements AvailabilityDatabaseInterface {

    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection) {
        $this->connection = $connection;
    }

    public function create(\App\Availability $availability): void {
        $this->connection->execute(
            "INSERT INTO availability (
                day,
                start_time,
                end_time
            ) VALUES (
                :day,
                :startTime,
                :endTime
            )",
            [
                ':day' => $availability->day,
                ":startTime" => $availability->time->start,
                ":endTime" => $availability->time->end
            ]
        );

        $availability->userId = $this->connection->lastInsertId();
    }

    public function read(string $userId): array {
        $result = $this->connection->query(
            "SELECT day_of_week, start_hour, end_hour FROM user_availability WHERE user_id = :user_id",
            [':user_id' => $userId]
        );

        $availabilities = [];
        foreach ($result as $row) {
            $availability = new \App\Availability();
            $availability->userId = $userId;
            $availability->day = \App\DayOfWeek::from($row['day_of_week']);
            $availability->time = new \App\TimeRange($row['start_hour'], $row['end_hour']);
            $availabilities[] = $availability;
        }

        return $availabilities;
    }

    public function update(\App\Availability $availability): void {
        $this->connection->execute(
            "UPDATE availability SET day_of_week = :day, start_hour = :startTime, end_hour = :endTime WHERE user_id = :user_id",
            [
                ':user_id' => $availability->userId,
                ':day' => $availability->day,
                ":startTime" => $availability->time->start,
                ":endTime" => $availability->time->end
            ]
        );
    }

    public function delete(string $userId): void {
        $this->connection->execute(
            "DELETE FROM availability WHERE user_id = :user_id",
            [':user_id' => $userId]
        );
    }
}