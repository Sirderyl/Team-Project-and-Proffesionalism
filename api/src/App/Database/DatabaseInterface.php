<?php

namespace App\Database;

/**
 * Interface for the database API
 * @author Kieran
 */
interface DatabaseInterface
{
    public function activities(): ActivityDatabaseInterface;
    public function users(): UsersDatabaseInterface;

    /**
     * Begin a transaction. Should be called before any setters are called
     */
    public function beginTransaction(): void;

    /**
     * Commit a transaction. Should be called after all setters have been called
     */
    public function commit(): void;

    /**
     * Rollback a transaction. Should be called in the catch block of a try-catch statement
     */
    public function rollback(): void;
}
