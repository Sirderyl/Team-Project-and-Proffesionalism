<?php

namespace App\Database;

/**
 * Interface for the database API
 * @author Kieran
 */
interface DatabaseInterface
{
    public function users(): UsersDatabaseInterface;
}
