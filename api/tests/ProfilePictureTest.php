<?php

use PHPUnit\Framework\TestCase;
use App\Database;
use App\Debug;

class ProfilePictureTest extends TestCase {
    private Database\DatabaseInterface $database;
    private Faker\Generator $faker;
    private App\ProfilePicture $endpoint;
    private App\User $user;
    private string $imageData;

    protected function setUp(): void {
        $this->faker = Faker\Factory::create();
        $this->database = Debug\DebugDatabase::createTestDatabase();
        $this->endpoint = new App\ProfilePicture($this->database);

        [$this->user] = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($this->user);

        // The endpoint does not modify/interpret the image data, so we can
        // use a password to simulate random bytes
        // If the endpoint did work with data (e.g., resizing images), we would
        // need a real jpeg to test with
        $this->imageData = $this->faker->password(512, 512);
    }

    /** Test that the endpoint returns a SVG if the user has no profile picture */
    public function testReturnsSvgWhenNoProfilePictureSet(): void {
        // We don't care about the actual content, as it is an implementation detail
        $this->assertEquals(
            'image/svg+xml',
            $this->endpoint->executeGet($this->user->userId)[0]
        );
    }

    /** Test that the endpoint returns the profile picture as-is if it exists */
    public function testReturnsExistingProfilePicture(): void {
        $this->database->users()->setProfilePicture($this->user->userId, $this->imageData);
        $this->assertEquals(
            ['image/jpeg', $this->imageData],
            $this->endpoint->executeGet($this->user->userId)
        );
    }

    /** Test that the endpoint sets the profile picture in the database as-is */
    public function testPostUpdatesInDatabase(): void {
        $this->endpoint->executePost($this->user->userId, $this->imageData);
        $this->assertEquals(
            $this->imageData,
            $this->database->users()->getProfilePicture($this->user->userId)
        );
    }

    /** Test that the endpoint removes the profile picture from the database */
    public function testDeleteRemovesFromDatabase(): void {
        $this->database->users()->setProfilePicture($this->user->userId, $this->imageData);
        $this->endpoint->executeDelete($this->user->userId);

        $this->assertNull(
            $this->database->users()->getProfilePicture($this->user->userId)
        );
    }

    /** Test that the endpoint fails if the user does not exist */
    public function testFailsIfUserDoesNotExist(): void {
        $this->expectException(App\Database\NotFoundException::class);
        $this->endpoint->executeGet(42);
    }
}
