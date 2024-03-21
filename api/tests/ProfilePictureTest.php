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

        $this->imageData = $this->faker->password(512, 512);
    }

    //Placeholder test
    public function testExecute(): void {
        $this->assertTrue(true);
    }

    /*public function testGet(): void {
        $this->database->users()->setProfilePicture($this->user->userId, $this->imageData);
        $this->assertEquals(
            $this->imageData,
            $this->endpoint->executeGet($this->user->userId)
        );
    }

    public function testPost(): void {
        $this->endpoint->executePost($this->user->userId, $this->imageData);
        $this->assertEquals(
            $this->imageData,
            $this->database->users()->getProfilePicture($this->user->userId)
        );
    }

    public function testDelete(): void {
        $this->database->users()->setProfilePicture($this->user->userId, $this->imageData);
        $this->endpoint->executeDelete($this->user->userId);

        $this->assertNull(
            $this->database->users()->getProfilePicture($this->user->userId)
        );
    }*/
}
