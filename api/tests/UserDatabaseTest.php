<?php

use PHPUnit\Framework\TestCase;
use App\Database;
use App\Debug;

final class UserDatabaseTest extends TestCase
{
    private Faker\Generator $faker;
    private Database\DatabaseInterface $database;

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
}
