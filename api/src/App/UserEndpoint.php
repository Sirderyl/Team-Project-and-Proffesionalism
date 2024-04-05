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

    public function getUser(int $userId): array {
        // Return everything except password
        $user = $this->database->users()->getById($userId);
        return [
            'userId' => $user->userId,
            'isManager' => $user->isManager,
            'userName' => $user->userName,
            'availability' => $user->availability,
            'phoneNumber' => $user->phoneNumber,
            'email' => $user->email,
        ];
    }
 }
