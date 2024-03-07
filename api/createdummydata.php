<?php

/**
 * Generate dummy data and insert it into the database
 *
 * Only intended to be used on the command line and on a fresh database
 */

require_once './vendor/autoload.php';

const NUM_USERS = 50;
const NUM_ORGANIZATIONS = 20;

$connection = new App\Database\SqliteConnection("database.db");
$database = new App\Database\Database($connection);

$faker = Faker\Factory::create('en_GB');

$database->beginTransaction();

function getProfilePicture(): string {
    // All these checks for false, if only PHP had a way to handle exceptional cases

    // Get a random image for profile pictures. Don't want to
    // make too many requests to the server
    $dummyProfile = file_get_contents('https://thispersondoesnotexist.com');
    if ($dummyProfile === false) {
        throw new Exception('Failed to get dummy profile pictures');
    }

    // Resize the image, API returns 1024x1024 images which are overkill for a profile picture
    $img = imagecreatefromstring($dummyProfile);
    if (!($img instanceof GdImage)) {
        throw new Exception('Failed to create image from data');
    }
    $resized = imagescale($img, 256, 256);
    if ($resized === false) {
        throw new Exception('Failed to resize image');
    }

    $stream = fopen('php://memory', 'r+');
    if ($stream === false) {
        throw new Exception('Failed to open memory stream');
    }
    imagejpeg($resized, $stream);

    rewind($stream);
    $contents = stream_get_contents($stream);
    fclose($stream);
    if ($contents === false) {
        throw new Exception('Failed to read from memory stream');
    }
    return $contents;
}

$profilePicture = getProfilePicture();

$users = [];

for ($i = 0; $i < NUM_USERS; $i++) {
    $user = new App\User();
    // Normally a username would never include the password, but this is just dummy data
    // and we want to be able to log in as a dummy user
    $password = $faker->password();
    $user->userName = $faker->unique()->name() . ' password: ' . $password;
    $user->phoneNumber = $faker->unique()->e164PhoneNumber();
    $user->email = $faker->unique()->email();
    $user->passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $users[] = $user;

    foreach (App\DayOfWeek::cases() as $day) {
        if (rand(0, 100) > 50) {
            $start = $faker->numberBetween(9, 14);
            $end = $faker->numberBetween($start + 1, 17);

            $user->setAvailability($day, new App\TimeRange($start, $end));
        }
    }

    $database->users()->create($user);
    $database->users()->setProfilePicture($user->userId, $profilePicture);
}


$dummyActivityImg = file_get_contents('https://picsum.photos/200/200');
if ($dummyActivityImg === false) {
    throw new Exception('Failed to get dummy activity pictures');
}


for ($i = 0; $i < NUM_ORGANIZATIONS; $i++) {
    $organization = new App\Organization();
    $organization->name = $faker->unique()->company();
    $organization->admin_id = $users[rand(0, NUM_USERS - 1)]->userId;
    $database->organizations()->create($organization);

    // Make sure we have a wide range of activity counts, including 0
    $numActivities = min($i, 10);
    for ($act = 0; $act < $numActivities; $act++) {
        $activity = new App\Activity();
        $activity->organizationId = $organization->id;
        $activity->name = $faker->unique()->jobTitle();
        $activity->shortDescription = $faker->realText(100);
        $activity->longDescription = $faker->realText(400);
        $activity->neededVolunteers = rand(1, 5);
        $start = $faker->numberBetween(9, 14);
        $end = $faker->numberBetween($start + 1, 17);
        $activity->time = new App\TimeRange($start, $end);

        $database->activities()->create($activity);
        $database->activities()->setPreviewPicture($activity->id, $dummyActivityImg);
    }
}

$database->commit();
