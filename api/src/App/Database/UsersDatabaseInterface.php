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
    public function getByEmail(string $email): \App\User;

    /**
     * Get a user by their ID
     * @param int $id
     * @throws NotFoundException If the user is not found
     */
    public function getById(int $id): \App\User;

    /**
     * Get all users
     * @return \App\User[]
     */
    public function getAll(): array;

    /**
     * Create a new user. Sets the user's ID on the User object.
     */
    public function create(\App\User $user): void;

    /**
     * Get a user's profile picture
     * @return ?string The raw JPEG data, represents a byte array, or null if no picture
     */
    public function getProfilePicture(int $userId): ?string;

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

    /**
     * Get activities the user is assigned to do
     * @return array<array{
     *   activity: array{
     *     name: string,
     *     id: int,
     *     shortDescription: string
     *   },
     *   id: int,
     *   start: \DateTime,
     *   users: array<array{
     *     id: int,
     *     name: string
     *   }>
     * }>
     */
    public function getAssignedActivities(int $userId, ?\DateTime $earliestStart = null, ?\DateTime $latestStart = null): array;

    /**
     * Get organizations the user is a member of or the admin
     * @return array<array{
     *   id: int,
     *   name: string,
     *   status: string
     * }>
     */
    public function getOrganizations(int $userId): array;
}
