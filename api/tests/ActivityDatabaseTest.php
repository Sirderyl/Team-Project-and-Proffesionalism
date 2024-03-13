<?php

use PHPUnit\Framework\TestCase;
use App\Database;
use App\Debug;

class ActivityDatabaseTest extends TestCase {
    private Faker\Generator $faker;
    private Database\DatabaseInterface $database;

    protected function setUp(): void
    {
        $this->faker = Faker\Factory::create();
        $this->database = Debug\DebugDatabase::createTestDatabase();
    }

    private function runRoundTripTest(int $days): void
    {
        [$admin] = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($admin);
        $organization = Debug\DebugOrganization::createDummyOrganization($this->faker, $admin->userId);
        $this->database->organizations()->create($organization);

        $activity = Debug\DebugActivity::createDummyActivity($this->faker, $organization->id, $days);
        $this->database->activities()->create($activity);
        $output = $this->database->activities()->get($activity->id);

        $this->assertEquals($activity, $output);
    }

    public function testRoundTrip(): void
    {
        // Make sure it works with 0 days and more than 0 days
        $this->runRoundTripTest(0);
        $this->runRoundTripTest(5);
    }
}
