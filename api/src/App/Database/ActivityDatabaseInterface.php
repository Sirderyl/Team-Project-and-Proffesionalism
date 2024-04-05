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
     * Set or remove a rating for a user's activity
     * @param int $scheduleId `rowid` returned from `getAssignedActivities`
     */
    public function setRating(int $scheduleId, ?int $rating): void;

    /**
     * Get all ratings for all activities and users.
     * Does not include assigned activities without ratings.
     * @return \App\UserActivity[]
     */
    public function getAllUserRatings(): array;
}
