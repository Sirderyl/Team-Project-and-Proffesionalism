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
    [$user] = App\Debug\DebugUser::createDummyUser($faker, "$i@example.com", "password$i");
    $users[] = $user;

    $database->users()->create($user);
    $database->users()->setProfilePicture($user->userId, $profilePicture);
}


$dummyActivityImg = file_get_contents('https://picsum.photos/200/200');
if ($dummyActivityImg === false) {
    throw new Exception('Failed to get dummy activity pictures');
}


for ($i = 0; $i < NUM_ORGANIZATIONS; $i++) {
    $manager = App\Debug\DebugUser::createDummyUser($faker, "$i@manager.com", "password$i")[0];
    $database->users()->create($manager);

    $organization = App\Debug\DebugOrganization::createDummyOrganization($faker, $manager->userId);
    $database->organizations()->create($organization);
    $database->organizations()->setUserStatus((int)$organization->id, $manager->userId, App\UserOrganizationStatus::Manager);

    // Make sure we have a wide range of activity counts, including 0
    $numActivities = min($i, 10);
    for ($act = 0; $act < $numActivities; $act++) {
        $activity = App\Debug\DebugActivity::createDummyActivity($faker, $organization->id);

        $database->activities()->create($activity);
        $database->activities()->setPreviewPicture($activity->id, $dummyActivityImg);
    }

    // Add some users
    $numUsers = min($i, 10);
    for ($user = 0; $user < $numUsers; $user++) {
        $selected = $users[rand(0, NUM_USERS - 1)]->userId;
        $database->organizations()->setUserStatus($organization->id, $selected, App\UserOrganizationStatus::Member);
    }
}

$database->commit();
