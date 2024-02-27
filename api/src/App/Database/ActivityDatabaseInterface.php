<?php

namespace App\Database;

/**
 * Database interface for activities
 * @author Kieran
 */
interface ActivityDatabaseInterface {
    public function create(\App\Activity $activity): void;
}
