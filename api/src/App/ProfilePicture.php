<?php

namespace App;

/**
 * Implementation of /user/{id}/profilepicture endpoint
 * @author Kieran
 */
class ProfilePicture {
    // Dummy SVG image to use when no profile picture is set
    private const DUMMY_PICTURE_SVG = <<<SVG
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        style="background-color: lightgray;"
    >
    <title>no profile picture</title>
    <text
        textLength="24"
        x="8"
        y="5"
        style="font-size: 20px; dominant-baseline: hanging;"
    >?</text>
    </svg>
    SVG;


    private Database\DatabaseInterface $database;

    public function __construct(Database\DatabaseInterface $database) {
        $this->database = $database;
    }

    /**
     * Get the raw data for a user's profile picture, returns a dummy
     * image if there is no profile picture set
     *
     * @return array{string, string} The content type and the raw data
     */
    public function executeGet(int $userId): array {
        $data = $this->database->users()->getProfilePicture($userId);

        if ($data !== null) {
            return ['image/jpeg', $data];
        } else {
            return ['image/svg+xml', self::DUMMY_PICTURE_SVG];
        }
    }

    public function executePost(int $userId, string $data): void {
        $this->database->users()->setProfilePicture($userId, $data);
    }

    public function executeDelete(int $userId): void {
        $this->database->users()->setProfilePicture($userId, null);
    }
}
