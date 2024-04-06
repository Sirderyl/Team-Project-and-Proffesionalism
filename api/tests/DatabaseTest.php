<?php

use App\Database\DatabaseInterface;
use App\Debug\DebugDatabase;
use App\Debug\DebugUser;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private Faker\Generator $faker;
    private DatabaseInterface $database;

    protected function setUp(): void
    {
        $this->faker = Faker\Factory::create();
        $this->database = DebugDatabase::createTestDatabase();
    }

    public function testTransaction(): void
    {
        [$user] = DebugUser::createDummyUser($this->faker);
        $this->database->beginTransaction();
        $this->database->users()->create($user);
        $this->database->commit();

        $output = $this->database->users()->getById($user->userId);
        $this->assertEquals($user, $output);
    }

    public function testRollback(): void
    {
        [$user] = DebugUser::createDummyUser($this->faker);
        $this->database->beginTransaction();
        $this->database->users()->create($user);
        $this->database->rollback();

        $this->expectException(\App\Database\NotFoundException::class);
        $this->database->users()->getById($user->userId);
    }

    public function testFailsIfDuplicateBeginTransaction(): void
    {
        $this->expectException(\PDOException::class);
        $this->database->beginTransaction();
        $this->database->beginTransaction();
    }

    public function testFailsIfCommitWithoutTransaction(): void
    {
        $this->expectException(\PDOException::class);
        $this->database->commit();
    }

    public function testFailsIfRollbackWithoutTransaction(): void
    {
        $this->expectException(\PDOException::class);
        $this->database->rollback();
    }
}
