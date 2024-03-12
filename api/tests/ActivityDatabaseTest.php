<?php

use PHPUnit\Framework\TestCase;
use App\Database;
use App\Debug;

class ActivityDatabaseTest extends TestCase {
    private Faker\Generator $faker;
    private Database\SqliteConnection $conn;
    private Database\Database $database;

    protected function setUp(): void
    {
        $this->faker = Faker\Factory::create();
        $this->conn = new Database\SqliteConnection(":memory:");
        $this->database = new Database\Database($this->conn);
    }

    public function testRoundTrip(): void
    {
        $admin = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($admin);
        $organization = Debug\DebugOrganization::createDummyOrganization($this->faker, $admin->userId);
        $this->database->organizations()->create($organization);

        $activity = Debug\DebugActivity::createDummyActivity($this->faker, $organization->id);
        $this->database->activities()->create($activity);
        $output = $this->database->activities()->get($activity->id);

        $this->assertEquals($activity, $output);
    }
}
