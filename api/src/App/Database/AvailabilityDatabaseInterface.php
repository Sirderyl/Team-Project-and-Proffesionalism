<?php

namespace App\Database;

/**
 * Database interface for availability
 * @author Filip
 */
interface AvailabilityDatabaseInterface {

    public function create(\App\Availability $availability): void;
    public function read(string $userId): array;
    public function update(\App\Availability $availability): void;
    public function delete(string $userId): void;
}
