<?php

use PHPUnit\Framework\TestCase;
use App\Database;
use App\Debug;

final class OrganizationDatabaseTest extends TestCase
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
        [$admin] = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($admin);
        $organization = Debug\DebugOrganization::createDummyOrganization($this->faker, $admin->userId);

        $this->database->organizations()->create($organization);
        $output = $this->database->organizations()->get($organization->id);

        $this->assertEquals($organization, $output);
    }
}
