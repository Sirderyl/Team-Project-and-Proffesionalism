<?php

use PHPUnit\Framework\TestCase;
use App\Database;
use App\Debug;

final class UserDatabaseTest extends TestCase
{
    private Faker\Generator $faker;
    private Database\DatabaseInterface $database;

    // Called before each test, not just once for the whole class
    // Important distinction as testGetAll relies on a fresh database
    protected function setUp(): void
    {
        $this->faker = Faker\Factory::create();
        $this->database = Debug\DebugDatabase::createTestDatabase();
    }

    public function testRoundTrip(): void
    {
        [$user] = Debug\DebugUser::createDummyUser($this->faker);

        $this->database->users()->create($user);
        $output = $this->database->users()->getByEmail($user->email);

        $this->assertEquals($user, $output);
    }

    public function testProfilePicture(): void
    {
        [$user] = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($user);

        // Image faker is deprecated, so just use a random string
        $picture = $this->faker->password(512, 512);
        $this->database->users()->setProfilePicture($user->userId, $picture);

        $output = $this->database->users()->getProfilePicture($user->userId);

        $this->assertEquals($picture, $output);
    }

    public function testPictureNotFoundIfNoUser(): void
    {
        $this->expectException(\App\Database\NotFoundException::class);
        $this->database->users()->getProfilePicture(1234);
    }

    public function testPictureNullIfNoPicture(): void
    {
        [$user] = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($user);

        $output = $this->database->users()->getProfilePicture($user->userId);

        $this->assertNull($output);
    }

    public function testGettersReturnSameResult(): void
    {
        [$user] = Debug\DebugUser::createDummyUser($this->faker);

        $this->database->users()->create($user);
        $output1 = $this->database->users()->getByEmail($user->email);
        $output2 = $this->database->users()->getById($output1->userId);

        $this->assertEquals($output1, $output2);
    }

    public function testGetAll(): void
    {
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            [$user] = Debug\DebugUser::createDummyUser($this->faker);
            $this->database->users()->create($user);
            $users[$user->userId] = $user;
        }

        $output = $this->database->users()->getAll();

        // We don't care about the order of the users, just that they're all there exactly once
        $this->assertCount(count($users), $output);
        foreach ($output as $user) {
            $this->assertEquals($users[$user->userId], $user);
            unset($users[$user->userId]);
        }
        $this->assertEmpty($users);
    }
}
