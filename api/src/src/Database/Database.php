<?php

namespace App\Database;

/**
 * Implementation of DatabaseInterface
 * @author Kieran
 */
class Database implements DatabaseInterface
{
    private readonly UsersDatabaseInterface $users;

    public function __construct(ConnectionInterface $connection)
    {
        $this->users = new UsersDatabase($connection);
    }

    public function users(): UsersDatabaseInterface
    {
        return $this->users;
    }
}
