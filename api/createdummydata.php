<?php

/**
 * Generate dummy data and insert it into the database
 *
 * Only intended to be used on the command line and on a fresh database
 */

require_once './vendor/autoload.php';

const NUM_USERS = 20;
const DAYS = [
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday',
    'Sunday'
];

$connection = new App\Database\SqliteConnection("database.db");
$database = new App\Database\Database($connection);

$faker = Faker\Factory::create();

$database->beginTransaction();

for ($i = 0; $i < NUM_USERS; $i++) {
    $user = new App\User();
    $user->userName = $faker->unique()->name();
    $user->availability = [];
    foreach (DAYS as $day) {
        if (rand(0, 100) > 50) {
            $start = rand(9, 14);
            $end = rand($start + 1, 17);

            $user->availability[$day] = [
                'startTime' => $start,
                'endTime' => $end
            ];
        }
    }

    $database->users()->create($user);
}

$database->commit();
