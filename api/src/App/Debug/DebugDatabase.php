<?php

namespace App\Debug;
use App\Database;

/**
 * Debugging functions for the database
 */
class DebugDatabase {
    private const SCHEMA_PATH = __DIR__ . '/../../../schema.sql';

    /**
     * Create an in-memory database for testing with the schema already applied
     */
    public static function createTestDatabase(): Database\DatabaseInterface {
        $connection = new Database\SqliteConnection(':memory:');
        self::runSchema($connection);
        return new Database\Database($connection);
    }

    /**
     * Run the schema script. Intended for in-memory databases only
     */
    public static function runSchema(Database\ConnectionInterface $connection): void {
        $schema = file_get_contents(self::SCHEMA_PATH);
        if ($schema === false) {
            throw new \RuntimeException('Failed to read schema');
        }

        foreach (explode(';', $schema) as $statement) {
            $connection->execute($statement);
        }
    }
}
