<?php

namespace App;

/**
 * A connection to a SQLite database
 * @author Kieran
 */
class SqliteConnection implements ConnectionInterface
{
    private \PDO $pdo;

    /**
     * Connect to a SQLite database
     * @param string $path The path to the database file, can be ":memory:" for an in-memory database
     */
    public function __construct(string $path)
    {
        $this->pdo = new \PDO("sqlite:$path");
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }


    // @phpstan-ignore-next-line - Return type depends on the query. Individual methods should specify the return type
    public function query(string $query, array $params = []): array
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function execute(string $query, array $params = []): int
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function lastInsertId(): string
    {
        $id = $this->pdo->lastInsertId();
        if ($id === false) {
            throw new \LogicException('Could not get last insert ID, was anything inserted?');
        }
        return $id;
    }
}