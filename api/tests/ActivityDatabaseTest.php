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

    public function testFailsIfInvalidId(): void
    {
        $this->expectException(\App\Database\NotFoundException::class);
        $this->database->activities()->get(1337);
    }

    public function testGetAll(): void
    {
        $activities = [];
        for ($i = 0; $i < 7; $i++) {
            [$admin] = Debug\DebugUser::createDummyUser($this->faker);
            $this->database->users()->create($admin);
            $organization = Debug\DebugOrganization::createDummyOrganization($this->faker, $admin->userId);
            $this->database->organizations()->create($organization);

            $activity = Debug\DebugActivity::createDummyActivity($this->faker, $organization->id, $i);
            $this->database->activities()->create($activity);
            $activities[$activity->id] = $activity;
        }

        $output = $this->database->activities()->getAll();

        // Check that all activities are in the output exactly once
        // If the counts are different, then this requirement must have been violated
        $this->assertCount(count($activities), $output);
        foreach ($output as $activity) {
            $this->assertEquals($activities[$activity->id], $activity);
            unset($activities[$activity->id]);
        }
        // If not empty, then there was an activity in $activities that wasn't in $output
        $this->assertEmpty($activities);
    }

    private function prepareActivityForAssignment(): \App\Activity
    {
        [$admin] = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($admin);
        $organization = Debug\DebugOrganization::createDummyOrganization($this->faker, $admin->userId);
        $this->database->organizations()->create($organization);
        $activity = Debug\DebugActivity::createDummyActivity($this->faker, $organization->id);
        $this->database->activities()->create($activity);
        return $activity;
    }

    public function testAssignToUserOk(): void
    {
        $activity = $this->prepareActivityForAssignment();

        [$user] = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($user);

        $dateTime = $this->faker->dateTime();
        $dateTime->setTime(15, 0, 0); // 3:00 PM
        $this->database->activities()->assignToUser($activity->id, $user->userId, $dateTime);

        $output = $this->database->users()->getAssignedActivities($user->userId);

        $this->assertCount(1, $output);
        $this->assertEquals([
            'activity' => [
                'name' => $activity->name,
                'id' => $activity->id,
                'rowid' => $output[0]["activity"]["rowid"],
                'shortDescription' => $activity->shortDescription
            ],
            'start' => $dateTime
        ], $output[0]);
    }

    public function testAssignToUserFailsIfDuplicate(): void
    {
        $activity = $this->prepareActivityForAssignment();

        [$user] = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($user);

        $dateTime = $this->faker->dateTime();
        $dateTime->setTime(15, 0, 0); // 3:00 PM
        $this->database->activities()->assignToUser($activity->id, $user->userId, $dateTime);

        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage('UNIQUE constraint failed');
        $this->database->activities()->assignToUser($activity->id, $user->userId, $dateTime);
    }
}
