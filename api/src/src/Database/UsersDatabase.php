<?php

namespace App\Database;

/**
 * Implementation of UsersDatabaseInterface
 * @author Kieran
 */
class UsersDatabase implements UsersDatabaseInterface
{
    private readonly ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function getPasswordHash(string $email): array
    {
        $result = $this->connection->query(
            'SELECT id, password_hash FROM users WHERE email = :email COLLATE NOCASE',
            ['email' => $email]
        );

        if (empty($result)) {
            throw new NotFoundException();
        }

        return $result[0];
    }

    public function create(array $data): string
    {
        $this->connection->execute(
            'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)',
            $data
        );

        return $this->connection->lastInsertId();
    }
}
