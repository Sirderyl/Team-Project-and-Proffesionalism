<?php

namespace App;

/**
 * Implementation of /user/{id}/profilepicture endpoint
 * @author Kieran
 */
class ProfilePicture {
    private Database\DatabaseInterface $database;

    public function __construct(Database\DatabaseInterface $database) {
        $this->database = $database;
    }

    /**
     * Get the raw JPEG data for a user's profile picture
     */
    public function execute(int $userId): string {
        return $this->database->users()->getProfilePicture($userId);
    }

    public function getContentType(): string {
        return 'image/jpeg';
    }
}