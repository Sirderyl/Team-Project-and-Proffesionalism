<?php

namespace App\Debug;

/**
 * Debug functions for activities
 */
class DebugActivity {
    public static function createDummyActivity(\Faker\Generator $faker, int $organizationId): \App\Activity {
        $activity = new \App\Activity();
        $activity->organizationId = $organizationId;
        $activity->name = $faker->unique()->jobTitle();
        $activity->shortDescription = $faker->realText(100);
        $activity->longDescription = $faker->realText(400);
        $activity->neededVolunteers = rand(1, 5);
        $start = $faker->numberBetween(9, 14);
        $end = $faker->numberBetween($start + 1, 17);
        $activity->time = new \App\TimeRange($start, $end);

        return $activity;
    }
}
