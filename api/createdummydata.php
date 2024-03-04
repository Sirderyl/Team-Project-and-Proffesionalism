<?php

/**
 * Generate dummy data and insert it into the database
 *
 * Only intended to be used on the command line and on a fresh database
 */

require_once './vendor/autoload.php';

const NUM_USERS = 50;
const NUM_ACTIVITIES = 20;
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

$faker = Faker\Factory::create('en_GB');

$database->beginTransaction();

for ($i = 0; $i < NUM_ACTIVITIES; $i++) {
    $activity = new App\Activity();
    $activity->name = $faker->unique()->realText(20);
    $activity->shortDescription = $faker->realText(100);
    $activity->longDescription = $faker->realText(400);
    $activity->neededVolunteers = rand(1, 5);
    $start = $faker->numberBetween(9, 14);
    $end = $faker->numberBetween($start + 1, 17);
    $activity->time = new App\TimeRange($start, $end);

    $database->activities()->create($activity);
}

for ($i = 0; $i < NUM_USERS; $i++) {
    $user = new App\User();
    // Normally a username would never include the password, but this is just dummy data
    // and we want to be able to log in as a dummy user
    $password = $faker->password();
    $user->userName = $faker->unique()->name() . ' password: ' . $password;
    $user->phoneNumber = $faker->unique()->e164PhoneNumber();
    $user->email = $faker->unique()->email();
    $user->passwordHash = password_hash($password, PASSWORD_DEFAULT);

    foreach (DAYS as $day) {
        if (rand(0, 100) > 50) {
            $start = $faker->numberBetween(9, 14);
            $end = $faker->numberBetween($start + 1, 17);

            $user->setAvailability(App\DayOfWeek::from($day), new App\TimeRange($start, $end));
        }
    }

    $database->users()->create($user);
}

$database->commit();
