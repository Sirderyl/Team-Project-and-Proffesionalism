<?php

namespace App;

/**
 * An activity that users can be assigned to
 * @author Kieran
 */
class Activity {
    /**
     * Factory method to create an Activity object from a database query result
     * @param array{
     *  id: int,
     *  organization_id: int,
     *  name: string,
     *  short_description: string,
     *  long_description: string,
     *  start_time: float,
     *  end_time: float,
     *  needed_volunteers: int
     * } $row
     */
    public static function fromRow(array $row): Activity {
        $activity = new Activity();
        $activity->id = $row['id'];
        $activity->organizationId = $row['organization_id'];
        $activity->name = $row['name'];
        $activity->shortDescription = $row['short_description'];
        $activity->longDescription = $row['long_description'];
        $activity->neededVolunteers = $row['needed_volunteers'];
        $activity->time = new TimeRange($row['start_time'], $row['end_time']);
        return $activity;
    }

    public int $id;
    public int $organizationId;

    public string $name;
    public string $shortDescription;
    public string $longDescription;

    // The number of volunteers needed for the activity
    public int $neededVolunteers;

    public TimeRange $time;
}
