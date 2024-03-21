<?php

namespace App\Debug;

/**
 * Debug functions for activities
 */
class DebugActivity {
    /**
     * @param ?int $scheduledDays The number of days the activity is scheduled for. If null, a random number between 0 and 7 is chosen
     */
    public static function createDummyActivity(\Faker\Generator $faker, int $organizationId, ?int $scheduledDays = null): \App\Activity {
        $scheduledDays = $scheduledDays ?? rand(0, 7);

        $activity = new \App\Activity();
        $activity->organizationId = $organizationId;
        $activity->name = $faker->unique()->jobTitle();
        $activity->shortDescription = $faker->realText(100);
        $activity->longDescription = $faker->realText(400);
        $activity->neededVolunteers = rand(1, 5);

        // Schedule for a random number of days, ensuring that the same day isn't scheduled twice
        while ($scheduledDays > 0) {
            $day = \App\DayOfWeek::fromIndex($faker->numberBetween(0, 6));
            if ($activity->getTime($day) === null) {
                $start = $faker->numberBetween(9, 15);
                $activity->setTime($day, new \App\TimeRange(
                    $start,
                    $faker->numberBetween($start+1, 17)
                ));
                $scheduledDays--;
            }
        }

        return $activity;
    }
}
