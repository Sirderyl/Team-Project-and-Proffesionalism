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

    public function testIsManagerTrue(): void
    {
        [$user] = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($user);

        $organization = Debug\DebugOrganization::createDummyOrganization($this->faker, $user->userId);
        $this->database->organizations()->create($organization);

        $output = $this->database->users()->getById($user->userId);
        $this->assertTrue($output->isManager);
    }

    public function testIsManagerFalse(): void
    {
        [$user] = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($user);

        $output = $this->database->users()->getById($user->userId);
        $this->assertFalse($output->isManager);
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
