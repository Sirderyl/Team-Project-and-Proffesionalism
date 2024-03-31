<?php

namespace App;

/**
 * Implementation of /activities endpoint
 * @author Filip
 */
class ActivitiesEndpoint {


    private Database\DatabaseInterface $database;

    public function __construct(Database\DatabaseInterface $database) {
        $this->database = $database;
    }

    public function getAll(): array {
        return $this->database->activities()->getAll();
    }

    public function assignToUser(int $activityId, int $userId, \DateTime $start): void {
        $this->database->activities()->assignToUser($activityId, $userId, $start);
    }
}
