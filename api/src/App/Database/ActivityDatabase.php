<?php

namespace App\Database;

/**
 * Implementation of ActivityDatabaseInterface
 */
class ActivityDatabase implements ActivityDatabaseInterface {
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection) {
        $this->connection = $connection;
    }

    public function create(\App\Activity $activity): void {
        $this->connection->execute(
            "INSERT INTO activity (
                name,
                short_description,
                long_description,
                start_time,
                end_time,
                needed_volunteers
            ) VALUES (
                :name,
                :shortDescription,
                :longDescription,
                :startTime,
                :endTime,
                :neededVolunteers
            )",
            [
                ':name' => $activity->name,
                ':shortDescription' => $activity->shortDescription,
                ':longDescription' => $activity->longDescription,
                ":startTime" => $activity->time->start,
                ":endTime" => $activity->time->end,

                ":neededVolunteers" => $activity->neededVolunteers
            ]
        );

        $activity->id = $this->connection->lastInsertId();
    }
}
