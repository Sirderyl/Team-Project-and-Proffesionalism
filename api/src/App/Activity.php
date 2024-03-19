<?php

namespace App;

/**
 * An activity that users can be assigned to
 * @author Kieran
 */
class Activity {
    /**
     * Factory method to create an Activity object from a database query result
     * @param array<array{
     *  id: int,
     *  organization_id: int,
     *  name: string,
     *  short_description: string,
     *  long_description: string,
     *  needed_volunteers: int,
     *  day_of_week: ?string,
     *  start_hour: ?float,
     *  end_hour: ?float
     * }> $rows
     * @return Activity[]
     */
    public static function fromRows(array $rows): array {
        $output = [];
        $current = new Activity();

        foreach ($rows as $row) {
            if (isset($current->id) && $current->id !== $row['id']) {
                $output[] = $current;
                $current = new Activity();
            }
            $current->id = $row['id'];
            $current->organizationId = $row['organization_id'];
            $current->name = $row['name'];
            $current->shortDescription = $row['short_description'];
            $current->longDescription = $row['long_description'];
            $current->neededVolunteers = $row['needed_volunteers'];
            if ($row['day_of_week'] !== null && $row['start_hour'] !== null && $row['end_hour'] !== null) {
                $current->setTime(
                    DayOfWeek::from($row['day_of_week']),
                    new TimeRange($row['start_hour'], $row['end_hour'])
                );
            }
        }
        $output[] = $current;

        return $output;
    }

    public int $id;
    public int $organizationId;

    public string $name;
    public string $shortDescription;
    public string $longDescription;

    // The number of volunteers needed for the activity
    public int $neededVolunteers;

    /**
     * @var array<string, TimeRange>
     * @see DayOfWeek
     */
    public array $times = [];

    /**
     * Set, replace, or remove the time for a given day
     */
    public function setTime(DayOfWeek $day, ?TimeRange $time): void {
        if ($time === null) {
            unset($this->times[$day->value]);
        } else {
            $this->times[$day->value] = $time;
        }
    }

    /**
     * Get the time for a given day, or null if there is no time set
     */
    public function getTime(DayOfWeek $day): ?TimeRange {
        return $this->times[$day->value] ?? null;
    }
}