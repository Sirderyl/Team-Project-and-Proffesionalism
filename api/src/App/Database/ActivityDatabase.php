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

    public function get(int $id): \App\Activity {
        $result = $this->connection->query(
            "SELECT
                activity.id,
                activity.organization_id,
                activity.name,
                activity.short_description,
                activity.long_description,
                activity.needed_volunteers,
                activity_time.day_of_week,
                activity_time.start_hour,
                activity_time.end_hour
            FROM activity
            LEFT JOIN activity_time ON activity.id = activity_time.activity_id
            WHERE activity.id = :id",
            [':id' => $id]
        );

        return \App\Activity::fromRows($result)[0];
    }

    public function create(\App\Activity $activity): void {
        $this->connection->execute(
            "INSERT INTO activity (
                organization_id,
                name,
                short_description,
                long_description,
                needed_volunteers
            ) VALUES (
                :organizationId,
                :name,
                :shortDescription,
                :longDescription,
                :neededVolunteers
            )",
            [
                ':organizationId' => $activity->organizationId,
                ':name' => $activity->name,
                ':shortDescription' => $activity->shortDescription,
                ':longDescription' => $activity->longDescription,
                ":neededVolunteers" => $activity->neededVolunteers
            ]
        );

        $activity->id = $this->connection->lastInsertId();

        foreach ($activity->times as $day => $time) {
            $this->connection->execute(
                "INSERT INTO activity_time (activity_id, day_of_week, start_hour, end_hour) VALUES (:activityId, :day, :start, :end)",
                [
                    ':activityId' => $activity->id,
                    ':day' => $day,
                    ':start' => $time->start,
                    ':end' => $time->end
                ]
            );
        }
    }

    public function setPreviewPicture(int $activityId, string $image): void {
        $this->connection->execute(
            "UPDATE activity SET preview_picture = :image WHERE id = :id",
            [
                ':id' => $activityId,
                ':image' => $image
            ]
        );
    }

    public function getPreviewPicture(int $activityId): string {
        $result = $this->connection->query(
            "SELECT preview_picture FROM activity WHERE id = :id",
            [':id' => $activityId]
        );

        return $result[0]['preview_picture'];
    }

    public function assignToUser(int $activityId, int $userId, \DateTime $start): void {
        $this->connection->execute(
            "INSERT INTO user_activity (user_id, activity_id, start_time) VALUES (:userId, :activityId, :start)",
            [
                ':userId' => $userId,
                ':activityId' => $activityId,
                ':start' => $start->format(\DATE_ISO8601),
            ]
        );
    }
}
