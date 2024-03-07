<?php

namespace App;

/**
 * Implementation of GET /activity/{id}
 * @phpstan-import-type TimeRangeArray from TimeRange
 */
class GetActivity
{
    private Database\DatabaseInterface $database;

    public function __construct(Database\DatabaseInterface $database)
    {
        $this->database = $database;
    }

    /**
     * Get an activity by its ID
     * @return array{
     *   name: string,
     *   description: string,
     *   time: TimeRangeArray,
     *   neededVolunteers: int,
     *   organization: array{
     *      name: string,
     *      id: string
     *   },
     * }
     */
    public function execute(string $id): array
    {
        $activity = $this->database->activities()->get($id);
        $organization = $this->database->organizations()->get($activity->organizationId);
        return [
            'name' => $activity->name,
            'description' => $activity->longDescription,
            'time' => $activity->time->toArray(),
            'neededVolunteers' => $activity->neededVolunteers,
            'organization' => [
                'name' => $organization->name,
                'id' => $organization->id
            ]
        ];
    }
}
