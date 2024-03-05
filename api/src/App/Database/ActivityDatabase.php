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

    public function get(string $id): \App\Activity {
        $row = $this->connection->query(
            "SELECT
                id,
                organization_id,
                name,
                short_description,
                long_description,
                start_time,
                end_time,
                needed_volunteers
            FROM activity
            WHERE id = :id",
            [':id' => $id]
        )[0];

        return \App\Activity::fromRow($row);
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

    public function setPreviewPicture(string $activityId, string $image): void {
        $this->connection->execute(
            "UPDATE activity SET preview_picture = :image WHERE id = :id",
            [
                ':id' => $activityId,
                ':image' => $image
            ]
        );
    }

    public function getPreviewPicture(string $activityId): string {
        $result = $this->connection->query(
            "SELECT preview_picture FROM activity WHERE id = :id",
            [':id' => $activityId]
        );

        return $result[0]['preview_picture'];
    }
}
