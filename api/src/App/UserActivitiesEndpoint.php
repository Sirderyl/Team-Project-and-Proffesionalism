<?php

namespace App;

/**
 * Implementation of /user/{id}/activities
 */
class UserActivitiesEndpoint
{
    private Database\DatabaseInterface $database;

    public function __construct(Database\DatabaseInterface $database)
    {
        $this->database = $database;
    }

    /**
     * Get the activities of a user
     * @param int $id The ID of the user
     * @return array<array{
     *   activity: array{
     *     name: string,
     *     id: int,
     *     shortDescription: string
     *   },
     *   start: string,
     * }>
     */
    public function execute(int $id): array
    {
        $assigned = $this->database->users()->getAssignedActivities($id);
        return array_map(fn ($row) => [
            'activity' => $row['activity'],
            'start' => $row['start']->format(\DateTime::ISO8601),
        ], $assigned);
    }
}
