<?php

namespace App\Database;

/**
 * Implementation of DatabaseInterface
 * @author Kieran
 */
class Database implements DatabaseInterface
{
    private readonly ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }
}
