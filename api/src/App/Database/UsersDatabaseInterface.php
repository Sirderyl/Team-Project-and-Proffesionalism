<?php

namespace App\Database;

/**
 * Interface for users in the database
 * @author Kieran
 */
interface UsersDatabaseInterface
{
    /**
     * Get a user by their email
     * @param string $email
     * @throws NotFoundException If the user is not found
     */
    public function get(string $email): \App\User;

    /**
     * Create a new user. Sets the user's ID on the User object.
     */
    public function create(\App\User $user): void;

    /**
     * Get a user's profile picture
     * @return string The raw JPEG data, represents a byte array
     */
    public function getProfilePicture(int $userId): string;

    /**
     * Set a user's profile picture
     * @param string|null $data The raw JPEG data, represents a byte array
     */
    public function setProfilePicture(int $userId, string|null $data): void;

    /**
     * Get the organizations the user has been invited to
     * @return array<\App\Organization>
     */
    public function getInvites(int $userId): array;
}
