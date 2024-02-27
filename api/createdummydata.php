<?php

/**
 * Generate dummy data and insert it into the database
 *
 * Only intended to be used on the command line and on a fresh database
 */

require_once './vendor/autoload.php';

const NUM_USERS = 20;

$connection = new App\Database\SqliteConnection("database.db");
$database = new App\Database\Database($connection);

$faker = Faker\Factory::create();

$database->beginTransaction();

for ($i = 0; $i < NUM_USERS; $i++) {
    $database->users()->create([
        'name' => $faker->unique()->name(),
        'email' => $faker->unique()->email(),
        'password_hash' => password_hash('password', PASSWORD_DEFAULT)
    ]);
}

$database->commit();
