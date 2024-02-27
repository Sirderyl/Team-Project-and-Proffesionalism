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
}
