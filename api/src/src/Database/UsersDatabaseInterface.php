<?php

namespace App\Database;

/**
 * Interface for users in the database
 * @author Kieran
 */
interface UsersDatabaseInterface
{
    /**
     * Get a user's password hash from their email
     * @param string $email
     * @return array{
     *  'id': int,
     *  'password_hash': string
     * }
     * @throws NotFoundException If the user is not found
     */
    public function getPasswordHash(string $email): array;

    /**
     * Create a new user
     * @param array{
     *  'name': string,
     *  'email': string,
     *  'password_hash': string
     * } $data
     * @return string The ID of the new user
     */
    public function create(array $data): string;
}
