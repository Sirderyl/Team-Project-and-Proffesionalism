<?php

use PHPUnit\Framework\TestCase;
use App\Database;
use App\Debug;

final class UserDatabaseTest extends TestCase
{
    private Faker\Generator $faker;
    private Database\SqliteConnection $conn;
    private Database\UsersDatabase $usersDatabase;

    protected function setUp(): void
    {
        $this->faker = Faker\Factory::create();
        // TODO: Need to run the schema script
        $this->conn = new Database\SqliteConnection(":memory:");
        $this->usersDatabase = new Database\UsersDatabase($this->conn);
    }

    public function testRoundTrip(): void
    {
        $user = Debug\DebugUser::createDummyUser($this->faker);

        $this->usersDatabase->create($user);
        $output = $this->usersDatabase->get($user->email);

        $this->assertEquals($user, $output);
    }
}
