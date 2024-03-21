<?php

use App\UserOrganizationStatus;
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

    public function testUserStatus(): void
    {
        [$admin] = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($admin);

        $organization = Debug\DebugOrganization::createDummyOrganization($this->faker, $admin->userId);
        $this->database->organizations()->create($organization);

        [$user] = Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($user);

        // Helper function to assert the status of a user
        $assertStatusEquals = function (UserOrganizationStatus $expected) use ($organization, $user): void {
            $this->assertEquals($expected, $this->database->organizations()->getUserStatus($organization->id, $user->userId));
        };

        // Initially, the user should not be a member of the organization
        $assertStatusEquals(UserOrganizationStatus::None);

        // When the user is added, they should be a member
        $this->database->organizations()->setUserStatus($organization->id, $user->userId, UserOrganizationStatus::Member);
        $assertStatusEquals(UserOrganizationStatus::Member);

        // When the user is removed, they should have no association
        $this->database->organizations()->setUserStatus($organization->id, $user->userId, UserOrganizationStatus::None);
        $assertStatusEquals(UserOrganizationStatus::None);

        // The user should no longer have a row if they are removed
        $result = $this->database->getConnection()->query(
            'SELECT * FROM user_organization WHERE organization_id = :organization_id AND user_id = :user_id',
            ['organization_id' => $organization->id, 'user_id' => $user->userId]
        );
        $this->assertCount(0, $result);
    }
}
