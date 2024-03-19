<?php

namespace App\Database;

/**
 * Database interface for activities
 * @author Kieran
 */
interface ActivityDatabaseInterface {
    public function get(int $id): \App\Activity;
    /**
     * Get all activities
     * @return \App\Activity[]
     */
    public function getAll(): array;
    public function create(\App\Activity $activity): void;

    public function setPreviewPicture(int $activityId, string $image): void;
    public function getPreviewPicture(int $activityId): string;

    public function assignToUser(int $activityId, int $userId, \DateTime $start): void;

    /**
     * Get all ratings for all activities and users.
     * Does not include assigned activities without ratings.
     * @return \App\Rating[]
     */
    public function getAllUserRatings(): array;
}
