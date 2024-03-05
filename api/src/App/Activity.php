<?php

namespace App;

/**
 * An activity that users can be assigned to
 * @author Kieran
 */
class Activity {
    public string $id;

    public string $name;
    public string $shortDescription;
    public string $longDescription;

    // The number of volunteers needed for the activity
    public int $neededVolunteers;

    public TimeRange $time;
}
