<?php

namespace App;

/**
 * Implementation of /user/{email} endpoint
 * @author Filip
 */

 class UserEndpoint {

    private Database\DatabaseInterface $database;

    public function __construct(Database\DatabaseInterface $database) {
        $this->database = $database;
    }

    public function getUser(string $email): User {
        return $this->database->users()->get($email);
    }
 }