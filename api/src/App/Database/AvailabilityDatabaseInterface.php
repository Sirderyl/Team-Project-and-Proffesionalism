<?php

namespace App\Database;

/**
 * Database interface for availability
 * @author Filip
 */
interface AvailabilityDatabaseInterface {

    public function add(\App\Availability $availability, int $userId): void;
    public function read(int $userId): array;
    public function update(\App\Availability $availability): void;
    public function delete(int $userId, \App\DayOfWeek $day): void;
}
