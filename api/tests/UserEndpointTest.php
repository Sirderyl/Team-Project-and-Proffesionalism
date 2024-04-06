<?php

use PHPUnit\Framework\TestCase;

class UserEndpointTest extends TestCase
{
    private Faker\Generator $faker;
    private App\Database\DatabaseInterface $database;
    private App\UserEndpoint $userEndpoint;

    public function setUp(): void
    {
        $this->faker = Faker\Factory::create();
        $this->database = App\Debug\DebugDatabase::createTestDatabase();
        $this->userEndpoint = new App\UserEndpoint($this->database);
    }

    public function testPasswordNotReturned(): void
    {
        [$user] = App\Debug\DebugUser::createDummyUser($this->faker);
        $this->database->users()->create($user);

        $result = $this->userEndpoint->getUser($user->userId);

        $this->assertEquals($user->userId, $result['userId']);
        $this->assertArrayNotHasKey('password', $result);
        $this->assertArrayNotHasKey('passwordHash', $result);
    }
}
