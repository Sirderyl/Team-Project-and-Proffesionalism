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

    public function execute(string $userId): array {
        return $this->database->availability()->read($userId);
    }
}
