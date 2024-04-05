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

    public function getAvailability(int $userId): array {
        return $this->database->availability()->read($userId);
    }

    public function addAvailability(int $userId, array $data): void {
        $availability = new Availability();
        $availability->day = DayOfWeek::from($data['day']);
        $availability->time = new TimeRange($data['start'], $data['end']);
        $this->database->availability()->add($availability, $userId);
    }

    public function updateAvailability(array $data): void {
        $availability = new Availability();
        $availability->day = DayOfWeek::from($data['day']);
        $availability->time = new TimeRange($data['start'], $data['end']);
        $this->database->availability()->update($availability);
    }

    public function deleteAvailability(int $userId, DayOfWeek $day): void {
        $this->database->availability()->delete($userId, $day);
    }
}
