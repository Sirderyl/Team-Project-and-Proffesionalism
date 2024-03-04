<?php

namespace App\Database;

/**
 * Implementation of DatabaseInterface
 * @author Kieran
 */
class Database implements DatabaseInterface
{
    private readonly ConnectionInterface $connection;
    private readonly ActivityDatabaseInterface $activities;
    private readonly UsersDatabaseInterface $users;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->activities = new ActivityDatabase($connection);
        $this->users = new UsersDatabase($connection);
    }

    public function activities(): ActivityDatabaseInterface
    {
        return $this->activities;
    }

    public function users(): UsersDatabaseInterface
    {
        return $this->users;
    }

    public function beginTransaction(): void
    {
        $this->connection->execute('BEGIN TRANSACTION');
    }

    public function commit(): void
    {
        $this->connection->execute('COMMIT');
    }

    public function rollback(): void
    {
        $this->connection->execute('ROLLBACK');
    }
}
