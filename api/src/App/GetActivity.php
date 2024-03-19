<?php

namespace App;

/**
 * Implementation of GET /activity/{id}
 * @phpstan-import-type TimeRangeArrayWithDay from TimeRange
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
     *   times: TimeRangeArrayWithDay[],
     *   neededVolunteers: int,
     *   organization: array{
     *      name: string,
     *      id: int
     *   },
     * }
     */
    public function execute(int $id): array
    {
        $activity = $this->database->activities()->get($id);
        $organization = $this->database->organizations()->get($activity->organizationId);
        return [
            'name' => $activity->name,
            'description' => $activity->longDescription,
            'times' => array_map(fn($day, $time) => [
                'day' => $day,
                'start' => $time->start,
                'end' => $time->end
            ], array_keys($activity->times), array_values($activity->times)),
            'neededVolunteers' => $activity->neededVolunteers,
            'organization' => [
                'name' => $organization->name,
                'id' => $organization->id
            ]
        ];
    }
}
