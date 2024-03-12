<?php

namespace App;

/**
 * Implementation of /user/{id}/availability endpoint
 * @author Filip
 */
class AvailabilityEndpoint {

    private Database\DatabaseInterface $database;

    public function __construct(Database\DatabaseInterface $database) {
        $this->database = $database;
    }

    public function getAvailability(string $userId): array {
        return $this->database->availability()->read($userId);
    }

    public function addAvailability(string $userId, array $data): void {
        $availability = new Availability();
        $availability->day = DayOfWeek::from($data['day']);
        $availability->time = new TimeRange($data['start'], $data['end']);
        $this->database->availability()->add($availability, $userId);
    }

    public function deleteAvailability(string $userId, string $day): void {
        $this->database->availability()->delete($userId, $day);
    }
}
