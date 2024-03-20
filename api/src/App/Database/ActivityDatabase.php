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

    /**
     * Backing method for all `get` methods
     * @param string $filter The WHERE clause of the SQL query. MUST NOT contain user-provided data
     * @param array<string, string|int> $params The parameters to bind to the query.
     * @return \App\Activity[] The activity that was found
     */
    private function runGet(string $filter, array $params): array {
        $result = $this->connection->query(
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
            $filter",
            $params
        );

        if (empty($result)) {
            throw new NotFoundException();
        }

        return array_map(fn ($row) => \App\Activity::fromRow($row), $result);
    }

    public function get(int $id): \App\Activity {
        return $this->runGet("WHERE id = :id", ['id' => $id])[0];
    }

    public function getAll(): array {
        return $this->runGet("", []);
    }

    public function create(\App\Activity $activity): void {
        $this->connection->execute(
            "INSERT INTO activity (
                organization_id,
                name,
                short_description,
                long_description,
                start_time,
                end_time,
                needed_volunteers
            ) VALUES (
                :organizationId,
                :name,
                :shortDescription,
                :longDescription,
                :startTime,
                :endTime,
                :neededVolunteers
            )",
            [
                ':organizationId' => $activity->organizationId,
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

    public function getAllUserRatings(): array
    {
        $result = $this->connection->query(
            "SELECT user_id, activity_id, rating FROM user_activity WHERE rating IS NOT NULL",
            []
        );

        return array_map(fn ($row) => new \App\Rating(
            $row['user_id'],
            $row['activity_id'],
            $row['rating']
        ), $result);
    }
}